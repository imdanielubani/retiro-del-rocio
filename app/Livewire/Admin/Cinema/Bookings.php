<?php

namespace App\Livewire\Admin\Cinema;

use App\Mail\CinemaBookingCancelled;
use App\Mail\CinemaBookingConfirmation;
use App\Models\CinemaBooking;
use App\Models\Movie;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Bookings extends Component
{
    use WithPagination;

    public string $search = '';

    public string $range = '';   // '' | today | 7d | 30d | month

    public bool $showFilters = false;

    public string $year = '';

    public string $month = '';

    public string $from = '';

    public string $to = '';

    public string $status = '';   // confirmed | used | cancelled

    public string $payment = '';  // paid | pending | refunded

    public string $movieFilter = '';

    // Detail
    public bool $showDetail = false;

    public ?int $selectedId = null;

    // Add modal
    public bool $showCreate = false;

    public string $cName = '';

    public string $cEmail = '';

    public string $cPhone = '';

    public $cMovieId = '';

    public string $cDate = '';

    public string $cTime = '';

    public $cAdult = 1;

    public $cChild = 0;

    public string $cSeats = '';

    public bool $cMarkPaid = true;

    protected $validationAttributes = [
        'cName' => 'name', 'cEmail' => 'email', 'cDate' => 'date', 'cMovieId' => 'movie',
    ];

    public function updating($name): void
    {
        if (in_array($name, ['search', 'range', 'year', 'month', 'from', 'to', 'status', 'payment', 'movieFilter'], true)) {
            $this->resetPage();
        }
    }

    public function setRange(string $r): void
    {
        $this->range = $this->range === $r ? '' : $r;
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset(['search', 'range', 'year', 'month', 'from', 'to', 'status', 'payment', 'movieFilter']);
        $this->resetPage();
    }

    /* ---- Row actions ---- */

    public function viewDetails(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function setStatus(int $id, string $status): void
    {
        CinemaBooking::where('id', $id)->update(['status' => $status]);
        $this->dispatch('toast', type: 'success', message: 'Booking marked '.$status.'.');
    }

    public function recordPayment(int $id): void
    {
        $b = CinemaBooking::find($id);
        if (! $b) {
            return;
        }
        $b->update(['payment_status' => 'paid', 'paid_at' => $b->paid_at ?? now()]);
        $this->dispatch('toast', type: 'success', message: 'Payment recorded for '.$b->code.'.');
    }

    public function cancelBooking(int $id): void
    {
        $b = CinemaBooking::find($id);
        if (! $b) {
            return;
        }
        $b->update([
            'status' => 'cancelled',
            'payment_status' => $b->payment_status === 'paid' ? 'refunded' : $b->payment_status,
        ]);
        // Free the seats so they can be booked again.
        \App\Models\CinemaSeatHold::where('cinema_booking_id', $b->id)->delete();
        $this->safeMail($b->customer_email, fn () => new CinemaBookingCancelled($b->fresh()));
        $this->dispatch('toast', type: 'success', message: 'Booking '.$b->code.' cancelled — guest notified, refund marked.');
    }

    /** Send a mailable without letting a mail failure break the action. */
    protected function safeMail(?string $email, \Closure $make): void
    {
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send($make());
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /* ---- Add booking ---- */

    public function openCreate(): void
    {
        $this->reset(['cName', 'cEmail', 'cPhone', 'cMovieId', 'cDate', 'cTime', 'cAdult', 'cChild', 'cSeats', 'cMarkPaid']);
        $this->cAdult = 1;
        $this->cChild = 0;
        $this->cMarkPaid = true;
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function createBooking(): void
    {
        $data = $this->validate([
            'cName' => ['required', 'string', 'max:160'],
            'cEmail' => ['required', 'email', 'max:190'],
            'cPhone' => ['nullable', 'string', 'max:40'],
            'cMovieId' => ['required', 'integer', 'exists:movies,id'],
            'cDate' => ['required', 'date'],
            'cTime' => ['required', 'string', 'max:30'],
            'cAdult' => ['required', 'integer', 'min:0', 'max:20'],
            'cChild' => ['required', 'integer', 'min:0', 'max:20'],
            'cSeats' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['cAdult'] + $data['cChild'] < 1) {
            $this->addError('cAdult', 'Add at least one ticket.');

            return;
        }

        $movie = Movie::find($data['cMovieId']);
        $seats = collect(preg_split('/[,\s]+/', $this->cSeats))->map(fn ($s) => trim($s))->filter()->values()->all();
        $amount = $data['cAdult'] * (int) $movie->adult_price + $data['cChild'] * (int) $movie->child_price;

        $b = CinemaBooking::create([
            'code' => CinemaBooking::makeCode(),
            'reference' => 'CIN-ADM-'.strtoupper(Str::random(8)),
            'movie_id' => $movie->id,
            'movie_title' => $movie->title,
            'show_date' => $data['cDate'],
            'show_time' => $data['cTime'],
            'seats' => $seats,
            'adult_tickets' => $data['cAdult'],
            'child_tickets' => $data['cChild'],
            'snacks' => [],
            'amount' => $amount,
            'customer_name' => $data['cName'],
            'customer_email' => $data['cEmail'],
            'customer_phone' => $data['cPhone'] ?: null,
            'status' => 'confirmed',
            'payment_status' => $this->cMarkPaid ? 'paid' : 'pending',
            'payment_method' => 'manual',
            'paid_at' => $this->cMarkPaid ? now() : null,
        ]);

        // Reserve the seats so the public site can't double-book them.
        if ($seats) {
            \App\Models\CinemaSeatHold::claimForBooking($movie->id, $data['cDate'], $data['cTime'], $seats, $b->reference, $b);
        }

        if ($b->customer_email) {
            try {
                Mail::to($b->customer_email)->send(new CinemaBookingConfirmation($b));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->showCreate = false;
        $this->dispatch('toast', type: 'success', message: 'Booking '.$b->code.' created — guest notified.');
    }

    protected function baseQuery()
    {
        return CinemaBooking::query()
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('code', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_email', 'like', "%{$this->search}%")
                ->orWhere('movie_title', 'like', "%{$this->search}%")))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->payment, fn ($q) => $q->where('payment_status', $this->payment))
            ->when($this->movieFilter, fn ($q) => $q->where('movie_id', $this->movieFilter))
            ->when($this->year, fn ($q) => $q->whereYear('show_date', $this->year))
            ->when($this->month, fn ($q) => $q->whereMonth('show_date', $this->month))
            ->when($this->from, fn ($q) => $q->whereDate('show_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('show_date', '<=', $this->to))
            ->when($this->range, function ($q) {
                [$s, $e] = $this->rangeBounds();
                if ($s) {
                    $q->whereBetween('show_date', [$s->toDateString(), $e->toDateString()]);
                }
            });
    }

    protected function rangeBounds(): array
    {
        $now = Carbon::now();

        return match ($this->range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d' => [$now->copy()->startOfDay(), $now->copy()->addDays(6)->endOfDay()],
            '30d' => [$now->copy()->startOfDay(), $now->copy()->addDays(29)->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default => [null, null],
        };
    }

    public function render()
    {
        $total = CinemaBooking::count();
        $upcoming = CinemaBooking::where('status', 'confirmed')->whereDate('show_date', '>=', now()->toDateString())->count();
        $today = CinemaBooking::whereDate('show_date', now()->toDateString())->where('status', '!=', 'cancelled')->count();
        $revenue = (int) CinemaBooking::where('payment_status', 'paid')->sum('amount');

        $stats = [
            ['label' => 'Total Bookings', 'value' => $total, 'sub' => 'All time', 'accent' => '#f38c00'],
            ['label' => 'Upcoming', 'value' => $upcoming, 'sub' => 'Confirmed shows', 'accent' => '#16a34a'],
            ['label' => 'Today', 'value' => $today, 'sub' => 'Showing today', 'accent' => '#0891b2'],
            ['label' => 'Revenue', 'value' => '₦'.number_format($revenue), 'sub' => 'Tickets + snacks', 'accent' => '#7c3aed'],
        ];

        $hasFilters = (bool) ($this->search || $this->range || $this->year || $this->month
            || $this->from || $this->to || $this->status || $this->payment || $this->movieFilter);

        $filteredCount = (clone $this->baseQuery())->count();
        $bookings = $this->baseQuery()->latest('id')->paginate(8);

        $years = CinemaBooking::query()->selectRaw('DISTINCT YEAR(COALESCE(show_date, created_at)) as y')
            ->orderByDesc('y')->pluck('y')->filter()->values();

        $movies = Movie::ordered()->get(['id', 'title']);
        $selected = $this->showDetail && $this->selectedId ? CinemaBooking::find($this->selectedId) : null;

        return view('admin.cinema.bookings', [
            'bookings' => $bookings,
            'stats' => $stats,
            'hasFilters' => $hasFilters,
            'filteredCount' => $filteredCount,
            'years' => $years,
            'movies' => $movies,
            'selected' => $selected,
        ])->layout('components.admin.app', [
            'title' => 'Cinema Bookings',
            'subtitle' => 'Movie ticket bookings made on the cinema page',
        ]);
    }
}
