<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaBooking extends Model
{
    protected $fillable = [
        'reference', 'services', 'guests', 'date', 'time', 'special_request',
        'subtotal', 'fees', 'taxes', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'status', 'payment_method', 'paid_at',
    ];

    protected $casts = [
        'services' => 'array',
        'guests' => 'integer',
        'date' => 'date',
        'subtotal' => 'integer',
        'fees' => 'integer',
        'taxes' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function totalLabel(): string
    {
        return '₦'.number_format($this->total);
    }

    /** Comma-separated service names, e.g. "Skin Care, Massage". */
    public function servicesLabel(): string
    {
        return collect($this->services ?? [])->pluck('name')->filter()->implode(', ') ?: '—';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Paid',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'paid' => '#16a34a',
            'pending' => '#d97706',
            'cancelled' => '#dc2626',
            default => '#6b7280',
        };
    }
}
