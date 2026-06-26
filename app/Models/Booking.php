<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reference', 'room_id', 'room_name', 'guests', 'check_in', 'check_out', 'nights',
        'amount', 'customer_name', 'customer_email', 'customer_phone',
        'pickup_vehicle', 'pickup_price', 'pickup_passengers', 'pickup_location',
        'pickup_arrival_date', 'pickup_time', 'pickup_flight_number',
        'status', 'payment_method', 'paid_at',
        'refund_amount', 'refund_method', 'refund_status', 'room_unit_id',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'pickup_arrival_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'integer',
        'nights' => 'integer',
        'guests' => 'integer',
        'pickup_passengers' => 'integer',
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

    /**
     * Auto-allocate an available physical room number for this booking's dates.
     * Date-aware: a unit is only unavailable when another active booking overlaps
     * the same dates, so numbers are reused across non-overlapping stays.
     * Returns the assigned RoomUnit, or null when none are configured / free.
     */
    public function autoAssignRoomUnit(): ?RoomUnit
    {
        if (! $this->room_id || $this->room_unit_id) {
            return $this->roomUnit; // nothing to do / already assigned
        }

        $room = $this->room ?: Room::find($this->room_id);
        if (! $room) {
            return null;
        }

        $in = optional($this->check_in)->toDateString();
        $out = optional($this->check_out)->toDateString();

        foreach ($room->units()->get() as $unit) {
            $clashes = self::query()
                ->where('room_unit_id', $unit->id)
                ->where('id', '!=', $this->id ?? 0)
                ->whereIn('status', ['paid', 'checked_in'])
                ->when($in && $out, fn ($q) => $q
                    ->whereDate('check_in', '<', $out)
                    ->whereDate('check_out', '>', $in))
                ->exists();

            if (! $clashes) {
                $this->room_unit_id = $unit->id;
                $this->save();

                return $unit;
            }
        }

        return null; // fully allocated for these dates — admin can assign manually
    }

    /* ---------------- Airport pick-up (transport) ---------------- */

    public function isPickup(): bool
    {
        return ! empty($this->pickup_vehicle);
    }

    // Transport booking reference shown on the Airport Pickup module, e.g. TR-1041.
    public function transportCode(): string
    {
        return 'TR-'.(1000 + $this->id);
    }

    public function pickupAmount(): int
    {
        return (int) preg_replace('/[^0-9]/', '', (string) $this->pickup_price);
    }

    public function pickupAmountLabel(): string
    {
        return '₦'.number_format($this->pickupAmount());
    }

    public function pickupPassengers(): int
    {
        return (int) ($this->pickup_passengers ?: $this->guests ?: 1);
    }

    public function pickupPassengersLabel(): string
    {
        $n = $this->pickupPassengers();

        return $n.' '.($n === 1 ? 'passenger' : 'passengers');
    }

    // Pick-up always runs from the airport to the hotel.
    public function pickupFrom(): string
    {
        return $this->pickup_location ?: 'Yakubu Gowon Airport (JOS)';
    }

    public function pickupTo(): string
    {
        return 'Hotel Del Retiro';
    }

    public function pickupInitials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->customer_name)) ?: [];
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';

        return strtoupper($a.$b) ?: 'G';
    }

    // Payment line shown on the transport detail modal.
    public function pickupPaymentLabel(): string
    {
        return match ($this->status) {
            'paid', 'checked_in', 'checked_out' => 'Payment Received',
            'cancelled' => $this->refund_status === 'completed' ? 'Refund Processed' : 'Payment Cancelled',
            default => 'Payment Pending',
        };
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

    // Where this transaction came from (room vs spa) for the Payments table.
    public function sourceLabel(): string
    {
        return 'Room';
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
