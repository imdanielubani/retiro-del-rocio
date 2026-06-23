<?php

namespace App\Livewire\Admin\Spa;

use App\Models\SpaBooking;
use Livewire\Component;
use Livewire\WithPagination;

class Bookings extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = ''; // '' | paid | pending | cancelled

    public function setStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function markCancelled(int $id): void
    {
        $b = SpaBooking::find($id);
        if (! $b) {
            return;
        }
        $b->update(['status' => 'cancelled']);
        $this->dispatch('toast', type: 'success', message: 'Reservation '.$b->reference.' was cancelled.');
    }

    public function render()
    {
        $base = SpaBooking::query();
        $total = (clone $base)->count();
        $paid = (clone $base)->where('status', 'paid')->count();
        $cancelled = (clone $base)->where('status', 'cancelled')->count();
        $revenue = (int) (clone $base)->where('status', 'paid')->sum('total');

        $stats = [
            ['label' => 'Total Reservations', 'value' => $total, 'sub' => 'All time', 'accent' => '#f38c00'],
            ['label' => 'Paid', 'value' => $paid, 'sub' => 'Confirmed', 'accent' => '#16a34a'],
            ['label' => 'Cancelled', 'value' => $cancelled, 'sub' => 'Cancelled', 'accent' => '#dc2626'],
            ['label' => 'Revenue', 'value' => '₦'.number_format($revenue), 'sub' => 'From paid bookings', 'accent' => '#7c3aed'],
        ];

        $bookings = SpaBooking::query()
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('reference', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_email', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(12);

        return view('admin.spa.bookings', [
            'bookings' => $bookings,
            'stats' => $stats,
            'counts' => [
                'all' => $total,
                'paid' => $paid,
                'pending' => (clone $base)->where('status', 'pending')->count(),
                'cancelled' => $cancelled,
            ],
        ])->layout('components.admin.app', [
            'title' => 'Spa Bookings',
            'subtitle' => 'Reservations made on the spa & wellness page',
        ]);
    }
}
