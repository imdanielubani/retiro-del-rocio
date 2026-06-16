<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reference', 'room_id', 'room_name', 'guests', 'check_in', 'check_out', 'nights',
        'amount', 'customer_name', 'customer_email', 'customer_phone',
        'pickup_vehicle', 'pickup_price', 'status', 'payment_method', 'paid_at',
        'refund_amount', 'refund_method', 'refund_status', 'room_unit_id',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'integer',
        'nights' => 'integer',
        'guests' => 'integer',
        'refund_amount' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function roomUnit()
    {
        return $this->belongsTo(RoomUnit::class);
    }

    public function amountLabel(): string
    {
        return '₦'.number_format($this->amount);
    }

    // Transaction reference shown in the Payment module, e.g. TXN-8841.
    public function txnId(): string
    {
        return 'TXN-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    // Booking reference shown alongside the transaction, e.g. BK-2841.
    public function bookingCode(): string
    {
        return 'BK-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    // Cancellation reference, e.g. CAN-2841.
    public function cancellationCode(): string
    {
        return 'CAN-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function refundLabel(): string
    {
        return '₦'.number_format((int) $this->refund_amount);
    }

    public function refundStatusLabel(): string
    {
        return match ($this->refund_status) {
            'completed' => 'Completed',
            'declined' => 'Declined',
            'pending' => 'Pending',
            default => 'Pending',
        };
    }

    public function refundStatusBadge(): string
    {
        return match ($this->refund_status) {
            'completed' => 'bg-[#dcfce7] text-[#16a34a]',
            'declined' => 'bg-[#fee2e2] text-[#dc2626]',
            default => 'bg-[#fef3c7] text-[#d97706]',
        };
    }

    public function refundMethodLabel(): string
    {
        return match ($this->refund_method) {
            'bank_transfer' => 'Bank Transfer',
            'card_reversal' => 'Card Reversal',
            'hotel_credit' => 'Hotel Credit',
            default => '—',
        };
    }

    // Human label for the Paystack channel captured at checkout.
    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            'card' => 'Card',
            'bank' => 'Bank',
            'bank_transfer' => 'Bank Transfer',
            'ussd' => 'USSD',
            'qr' => 'QR',
            'mobile_money' => 'Mobile Money',
            'eft' => 'EFT',
            null, '' => 'Paystack',
            default => ucwords(str_replace('_', ' ', $this->payment_method)),
        };
    }

    // Refund method that mirrors how the guest originally paid (Paystack channel).
    public function defaultRefundMethod(): string
    {
        return match ($this->payment_method) {
            'card' => 'card_reversal',
            'bank', 'bank_transfer', 'ussd', 'eft' => 'bank_transfer',
            default => 'bank_transfer',
        };
    }

    // Payment-centric status (a checked-in guest has still simply "Paid").
    public function paymentStatusLabel(): string
    {
        return match ($this->status) {
            'paid', 'checked_in', 'checked_out' => 'Paid',
            'pending' => 'Pending',
            'cancelled' => $this->refund_status === 'completed' ? 'Refunded' : 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function paymentStatusBadge(): string
    {
        return match ($this->status) {
            'paid', 'checked_in', 'checked_out' => 'bg-[#dcfce7] text-[#16a34a]',
            'pending' => 'bg-[#fef3c7] text-[#d97706]',
            'cancelled' => 'bg-[#fee2e2] text-[#dc2626]',
            default => 'bg-[#f3f4f6] text-[#6b7280]',
        };
    }

    // Verbose payment status used on the booking details page.
    public function paymentSummaryStatus(): string
    {
        return match ($this->status) {
            'paid', 'checked_in', 'checked_out' => 'Paid in full',
            'pending' => 'Awaiting payment',
            'cancelled' => $this->refund_status === 'completed' ? 'Refunded' : 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    // Status as presented in the admin (paid → Confirmed).
    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Confirmed',
            'pending' => 'Pending',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    // Solid hex colour for calendar bars / legend dots.
    public function statusColor(): string
    {
        return match ($this->status) {
            'paid' => '#16a34a',
            'pending' => '#d97706',
            'checked_in' => '#7c3aed',
            'checked_out' => '#64748b',
            'cancelled' => '#dc2626',
            default => '#6b7280',
        };
    }

    // Tailwind classes for the status pill.
    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid' => 'bg-[#dcfce7] text-[#16a34a]',
            'pending' => 'bg-[#fef3c7] text-[#d97706]',
            'checked_in' => 'bg-[#ede9fe] text-[#7c3aed]',
            'checked_out' => 'bg-[#eef2f6] text-[#475569]',
            'cancelled' => 'bg-[#fee2e2] text-[#dc2626]',
            default => 'bg-[#f3f4f6] text-[#6b7280]',
        };
    }
}
