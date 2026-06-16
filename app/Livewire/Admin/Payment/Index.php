<?php

namespace App\Livewire\Admin\Payment;

use App\Models\Booking;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // Quick date filter: '' | today | 7d | 30d | month | last_month
    public string $range = '';

    public bool $showFilters = false;

    // Advanced filters.
    public string $year = '';

    public string $month = '';

    public string $day = '';

    public string $from = '';

    public string $to = '';

    public string $method = '';

    public string $status = '';

    public function updating($name): void
    {
        // Any filter change resets pagination.
        $this->resetPage();
    }

    public function setRange(string $range): void
    {
        $this->range = $this->range === $range ? '' : $range;
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function clearAll(): void
    {
        $this->reset(['search', 'range', 'year', 'month', 'day', 'from', 'to', 'method', 'status']);
        $this->resetPage();
    }

    // Build the filtered query shared by the table and the summary totals.
    protected function baseQuery()
    {
        $dateCol = 'paid_at';

        return Booking::query()
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('reference', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_email', 'like', "%{$this->search}%")
                ->orWhere('room_name', 'like', "%{$this->search}%")
                ->orWhere('id', 'like', "%{$this->search}%")))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->method, fn ($q) => $q->where('payment_method', $this->method))
            ->when($this->year, fn ($q) => $q->whereYear($dateCol, $this->year))
            ->when($this->month, fn ($q) => $q->whereMonth($dateCol, $this->month))
            ->when($this->day, fn ($q) => $q->whereDay($dateCol, $this->day))
            ->when($this->from, fn ($q) => $q->whereDate($dateCol, '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate($dateCol, '<=', $this->to))
            ->when($this->range, function ($q) use ($dateCol) {
                [$start, $end] = $this->rangeBounds();
                if ($start) {
                    $q->whereBetween($dateCol, [$start, $end]);
                }
            });
    }

    protected function rangeBounds(): array
    {
        $now = Carbon::now();

        return match ($this->range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '30d' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            default => [null, null],
        };
    }

    // ₦4.2M / ₦622,000 style.
    protected function naira(int $n): string
    {
        if ($n >= 1_000_000) {
            return '₦'.rtrim(rtrim(number_format($n / 1_000_000, 1), '0'), '.').'M';
        }

        return '₦'.number_format($n);
    }

    public function render()
    {
        $now = Carbon::now();

        // ---- Stat cards (all-time / month context) ----
        $monthRevenue = (int) Booking::where('status', 'paid')
            ->whereBetween('paid_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');

        $lastMonthRevenue = (int) Booking::where('status', 'paid')
            ->whereBetween('paid_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])
            ->sum('amount');

        $revenueDelta = $lastMonthRevenue > 0
            ? (int) round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : ($monthRevenue > 0 ? 100 : 0);

        $totalTransactions = Booking::count();

        $pendingAmount = (int) Booking::where('status', 'pending')->sum('amount');
        $pendingCount = Booking::where('status', 'pending')->count();

        $refundsAmount = (int) Booking::where('status', 'cancelled')->sum('amount');
        $refundsThisMonth = Booking::where('status', 'cancelled')
            ->whereBetween('updated_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $stats = [
            'revenue' => [
                'label' => 'Monthly Revenue',
                'value' => $this->naira($monthRevenue),
                'sub' => ($revenueDelta >= 0 ? '↑ ' : '↓ ').abs($revenueDelta).'% from '.$now->copy()->subMonthNoOverflow()->format('M'),
                'accent' => '#f38c00',
            ],
            'transactions' => [
                'label' => 'Total Transactions',
                'value' => number_format($totalTransactions),
                'sub' => $now->format('F Y'),
                'accent' => '#16a34a',
            ],
            'pending' => [
                'label' => 'Pending Payments',
                'value' => $this->naira($pendingAmount),
                'sub' => $pendingCount.' transaction'.($pendingCount === 1 ? '' : 's'),
                'accent' => '#d97706',
            ],
            'refunds' => [
                'label' => 'Refunds Issued',
                'value' => $this->naira($refundsAmount),
                'sub' => $refundsThisMonth.' this month',
                'accent' => '#dc2626',
            ],
        ];

        // ---- Filtered summary ----
        $summaryCount = (clone $this->baseQuery())->count();
        $summaryAmount = (int) (clone $this->baseQuery())->sum('amount');

        // Whether any filter is active (drives the "Clear all" button).
        $hasFilters = (bool) ($this->search || $this->range || $this->year || $this->month
            || $this->day || $this->from || $this->to || $this->method || $this->status);

        // ---- Table ----
        $transactions = $this->baseQuery()->latest('paid_at')->latest('id')->paginate(8);

        // ---- Filter option sources ----
        $years = Booking::query()
            ->selectRaw('DISTINCT YEAR(COALESCE(paid_at, created_at)) as y')
            ->orderByDesc('y')
            ->pluck('y')
            ->filter()
            ->values();

        $methods = Booking::query()
            ->whereNotNull('payment_method')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        return view('admin.payment.index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'summaryCount' => $summaryCount,
            'summaryAmount' => '₦'.number_format($summaryAmount),
            'hasFilters' => $hasFilters,
            'years' => $years,
            'methods' => $methods,
        ])->layout('components.admin.app', [
            'title' => 'Payments',
            'subtitle' => 'Transactions captured from checkout',
        ]);
    }
}
