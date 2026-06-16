<?php

namespace App\Livewire\Admin\Notifications;

use App\Models\Booking;
use App\Models\ContactMessage;
use Livewire\Component;

class Bell extends Component
{
    public function render()
    {
        $newMessages = ContactMessage::where('status', 'new')->latest()->take(5)->get();
        $newMessageCount = ContactMessage::where('status', 'new')->count();
        $recentBookings = Booking::latest()->take(4)->get();
        $notifCount = $newMessageCount + $recentBookings->where('created_at', '>=', now()->subDay())->count();

        return view('admin.notifications.bell', compact(
            'newMessages',
            'newMessageCount',
            'recentBookings',
            'notifCount',
        ));
    }
}
