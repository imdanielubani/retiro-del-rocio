<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\Booking;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Top tabs: all | calendar | approval | cancellations
    public string $tab = 'all';

    public string $search = '';

    // Quick date range: '' (all time) | today | 7d | 30d | month | last_month
    public string $range = '';

    public bool $showFilters = false;

    // Advanced filters.
    public string $status = '';

    public string $roomFilter = '';

    public string $from = '';

    public string $to = '';

    // Calendar: focused date (Y-m-d) + view mode (month | week | day).
    public string $calCursor = '';

    public string $calView = 'month';

    // Selected pending request in the Approval Queue.
    public ?int $approvalId = null;

    // Cancellations / Refund Processor.
    public ?int $cancelSelectedId = null;

    public string $refundPolicy = '';

    public int $refundAmount = 0;

    public string $refundMethod = 'bank_transfer';

    public function mount(): void
    {
        $this->calCursor = now()->format('Y-m-d');
    }

    public function selectCancellation(int $id): void
    {
        $this->cancelSelectedId = $id;
        $booking = Booking::find($id);
        if ($booking) {
            $this->refundAmount = (int) ($booking->refund_amount ?? $booking->amount);
            // Default the refund channel to however the guest actually paid.
            $this->refundMethod = $booking->refund_method ?: $booking->defaultRefundMethod();
            $this->refundPolicy = '';
        }
    }

    // Selecting a policy suggests a refund amount (% of the booking total).
    public function updatedRefundPolicy(string $value): void
    {
        $booking = $this->cancelSelectedId ? Booking::find($this->cancelSelectedId) : null;
        if (! $booking) {
            return;
        }
        $pct = match ($value) {
            'flexible' => 100,
            'moderate' => 80,
            'strict' => 50,
            default => null,
        };
        if ($pct !== null) {
            $this->refundAmount = (int) round($booking->amount * $pct / 100);
        }
    }

    public function issueRefund(int $id): void
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return;
        }
        $booking->refund_amount = max(0, (int) $this->refundAmount);
        $booking->refund_method = $this->refundMethod;
        $booking->refund_status = 'completed';
        $booking->save();

        $this->dispatch('toast', type: 'success', message: 'Refund of '.$booking->refundLabel().' issued for '.$booking->cancellationCode().'.');
    }

    public function declineRefund(int $id): void
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return;
        }
        $booking->refund_amount = 0;
        $booking->refund_status = 'declined';
        $booking->save();

        $this->dispatch('toast', type: 'success', message: 'Refund declined for '.$booking->cancellationCode().'.');
    }

    public function selectApproval(int $id): void
    {
        $this->approvalId = $id;
    }

    public function approve(int $id): void
    {
        $this->confirm($id);
        $this->approvalId = null;
    }

    public function decline(int $id): void
    {
        $this->cancel($id);
        $this->approvalId = null;
    }

    public function setCalView(string $view): void
    {
        if (in_array($view, ['month', 'week', 'day'], true)) {
            $this->calView = $view;
        }
    }

    public function updating($name): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function setRange(string $range): void
    {
        $this->range = $this->range === $range ? '' : $range;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'range', 'status', 'roomFilter', 'from', 'to']);
        $this->resetPage();
    }

    public function calPrev(): void
    {
        $this->shiftCursor(-1);
    }

    public function calNext(): void
    {
        $this->shiftCursor(1);
    }

    protected function shiftCursor(int $dir): void
    {
        $c = Carbon::parse($this->calCursor);
        $c = match ($this->calView) {
            'week' => $c->addWeeks($dir),
            'day' => $c->addDays($dir),
            default => $c->addMonthsNoOverflow($dir),
        };
        $this->calCursor = $c->format('Y-m-d');
    }

    // ---- Existing functions (preserved) ----
    public function cancel(int $id): void
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return;
        }

        $booking->status = 'cancelled';
        // Queue a refund for processing in the Cancellations tab (don't overwrite a processed one).
        if (! $booking->refund_status) {
            $booking->refund_status = 'pending';
        }
        $this->freeUnit($booking);
        $booking->save();

        $this->dispatch('toast', type: 'success', message: $booking->bookingCode().' cancelled.');
    }

    // Release the room number a booking currently holds.
    protected function freeUnit(Booking $booking): void
    {
        if ($booking->room_unit_id) {
            \App\Models\RoomUnit::where('id', $booking->room_unit_id)
                ->where('booking_id', $booking->id)
                ->update(['status' => 'available', 'booking_id' => null]);
        }
    }

    // ---- New status actions (design) ----
    public function confirm(int $id): void
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return;
        }

        $booking->status = 'paid';
        $booking->save();

        $this->dispatch('toast', type: 'success', message: $booking->bookingCode().' confirmed.');
    }

    public function checkOut(int $id): void
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return;
        }

        $this->freeUnit($booking);
        $booking->status = 'checked_out';
        $booking->save();

        $this->dispatch('toast', type: 'success', message: $booking->bookingCode().' checked out — room is now available.');
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

    // Status forced by the active tab, otherwise the advanced filter.
    protected function effectiveStatus(): string
    {
        return match ($this->tab) {
            'approval' => 'pending',
            'cancellations' => 'cancelled',
            default => $this->status,
        };
    }

    protected function baseQuery()
    {
        return Booking::query()
            ->with('room')
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('reference', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_email', 'like', "%{$this->search}%")
                ->orWhere('room_name', 'like', "%{$this->search}%")
                ->orWhere('id', 'like', "%{$this->search}%")))
            ->when($this->effectiveStatus(), fn ($q) => $q->where('status', $this->effectiveStatus()))
            ->when($this->roomFilter, fn ($q) => $q->where('room_name', $this->roomFilter))
            ->when($this->from, fn ($q) => $q->whereDate('check_in', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('check_in', '<=', $this->to))
            ->when($this->range, function ($q) {
                [$start, $end] = $this->rangeBounds();
                if ($start) {
                    $q->whereBetween('check_in', [$start, $end]);
                }
            });
    }

    public function render()
    {
        $total = Booking::count();
        $confirmed = Booking::whereIn('status', ['paid', 'checked_in', 'checked_out'])->count();
        $pending = Booking::where('status', 'pending')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();

        $stats = [
            'total' => ['label' => 'Total Bookings', 'value' => $total, 'sub' => 'All reservations', 'accent' => '#f38c00'],
            'confirmed' => ['label' => 'Confirmed', 'value' => $confirmed, 'sub' => ($total ? round($confirmed / $total * 100) : 0).'% of total', 'accent' => '#16a34a'],
            'pending' => ['label' => 'Pending', 'value' => $pending, 'sub' => 'Awaiting review', 'accent' => '#d97706'],
            'cancelled' => ['label' => 'Cancelled', 'value' => $cancelled, 'sub' => ($total ? round($cancelled / $total * 100) : 0).'% cancellation rate', 'accent' => '#dc2626'],
        ];

        $rooms = Booking::query()->whereNotNull('room_name')->distinct()->orderBy('room_name')->pluck('room_name');

        $bookings = $this->baseQuery()->latest('id')->paginate(8);
        $bookingsCount = (clone $this->baseQuery())->count();

        // Calendar data (only when the Calendar tab is active).
        $calendar = null;
        if ($this->tab === 'calendar') {
            $cursor = Carbon::parse($this->calCursor ?: now());

            if ($this->calView === 'week') {
                $gridStart = $cursor->copy()->startOfWeek(Carbon::SUNDAY);
                $gridEnd = $cursor->copy()->endOfWeek(Carbon::SATURDAY);
                $refMonth = null;
                $title = $gridStart->format('M j').' – '.$gridEnd->format('M j, Y');
            } elseif ($this->calView === 'day') {
                $gridStart = $cursor->copy()->startOfDay();
                $gridEnd = $cursor->copy()->endOfDay();
                $refMonth = null;
                $title = $cursor->format('l, j M Y');
            } else { // month
                $first = $cursor->copy()->startOfMonth();
                $gridStart = $first->copy()->startOfWeek(Carbon::SUNDAY);
                $gridEnd = $cursor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
                $refMonth = $first->month;
                $title = $first->format('F Y');
            }

            $grouped = Booking::query()
                ->whereNotNull('check_in')
                ->whereBetween('check_in', [$gridStart->copy()->startOfDay(), $gridEnd->copy()->endOfDay()])
                ->orderBy('check_in')
                ->get()
                ->groupBy(fn ($b) => $b->check_in->format('Y-m-d'));

            $days = [];
            for ($d = $gridStart->copy(); $d <= $gridEnd; $d->addDay()) {
                $days[] = [
                    'date' => $d->copy(),
                    'inMonth' => $refMonth === null ? true : $d->month === $refMonth,
                    'isToday' => $d->isToday(),
                    'isWeekend' => $d->isWeekend(),
                    'bookings' => $grouped->get($d->format('Y-m-d'), collect()),
                ];
            }

            $calendar = ['title' => $title, 'view' => $this->calView, 'days' => $days];
        }

        // Approval Queue data (only when the Approval tab is active).
        $approvalStats = null;
        $pendingRequests = collect();
        $approvalSelected = null;
        if ($this->tab === 'approval') {
            $today = today();
            $confirmedToday = Booking::where('status', 'paid')->whereDate('updated_at', $today)->count();
            $checkedInToday = Booking::where('status', 'checked_in')->whereDate('updated_at', $today)->count();
            $declinedToday = Booking::where('status', 'cancelled')->whereDate('updated_at', $today)->count();
            $pendingCount = Booking::where('status', 'pending')->count();

            // Average review time = created → decided, across decided bookings.
            $decided = Booking::whereIn('status', ['paid', 'checked_in', 'cancelled'])->get(['created_at', 'updated_at']);
            $avgHours = $decided->count()
                ? round($decided->avg(fn ($b) => $b->created_at->diffInMinutes($b->updated_at)) / 60, 1)
                : null;

            $approvalStats = [
                'approved' => ['label' => 'Approved Today', 'value' => $confirmedToday + $checkedInToday, 'sub' => $confirmedToday.' confirmed, '.$checkedInToday.' checked in', 'accent' => '#16a34a', 'clock' => false],
                'pending' => ['label' => 'Pending', 'value' => $pendingCount, 'sub' => 'Awaiting review', 'accent' => '#d97706', 'clock' => true],
                'declined' => ['label' => 'Declined Today', 'value' => $declinedToday, 'sub' => 'Cancelled reservations', 'accent' => '#dc2626', 'clock' => false],
                'avg' => ['label' => 'Avg Review Time', 'value' => $avgHours !== null ? $avgHours.' hrs' : '—', 'sub' => ($avgHours !== null && $avgHours < 6 ? 'Below 6hr target' : 'Review turnaround'), 'accent' => '#f38c00', 'clock' => false],
            ];

            $pendingRequests = Booking::with('room')->where('status', 'pending')->latest()->get();
            $approvalSelected = $pendingRequests->firstWhere('id', $this->approvalId) ?? $pendingRequests->first();
        }

        // Cancellations data (only when the Cancellations tab is active).
        $cancelStats = null;
        $cancelSelected = null;
        if ($this->tab === 'cancellations') {
            $now = Carbon::now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();

            $totalCancellations = Booking::where('status', 'cancelled')->count();
            $thisMonth = Booking::where('status', 'cancelled')->whereBetween('updated_at', [$monthStart, $monthEnd])->count();
            $refundsIssued = (int) Booking::where('refund_status', 'completed')->whereBetween('updated_at', [$monthStart, $monthEnd])->sum('refund_amount');
            $nonRefundable = (int) Booking::where('status', 'cancelled')->where('refund_status', 'declined')->sum('amount');

            $cancelStats = [
                'total' => ['label' => 'Total Cancellations', 'value' => $totalCancellations, 'sub' => 'All time', 'accent' => '#f38c00'],
                'month' => ['label' => 'This Month', 'value' => $thisMonth, 'sub' => $now->format('F Y'), 'accent' => '#d97706'],
                'refunds' => ['label' => 'Refunds Issued', 'value' => '₦'.number_format($refundsIssued), 'sub' => 'This month', 'accent' => '#dc2626'],
                'nonref' => ['label' => 'Non-Refundable', 'value' => '₦'.number_format($nonRefundable), 'sub' => 'Policy protected', 'accent' => '#16a34a'],
            ];

            // $bookings is already filtered to cancelled (effectiveStatus) + paginated.
            $cancelSelected = $bookings->firstWhere('id', $this->cancelSelectedId) ?? $bookings->first();
        }

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'bookingsCount' => $bookingsCount,
            'stats' => $stats,
            'rooms' => $rooms,
            'calendar' => $calendar,
            'approvalStats' => $approvalStats,
            'pendingRequests' => $pendingRequests,
            'approvalSelected' => $approvalSelected,
            'cancelStats' => $cancelStats,
            'cancelSelected' => $cancelSelected,
        ])->layout('components.admin.app', [
            'title' => 'Bookings',
            'subtitle' => 'Reservations captured from checkout',
        ]);
    }
}
