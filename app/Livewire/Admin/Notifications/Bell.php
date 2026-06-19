<?php

namespace App\Livewire\Admin\Notifications;

use App\Notifications\BookingReceived;
use App\Notifications\MessageReceived;
use Livewire\Component;

class Bell extends Component
{
    /** Mark all notifications read once the admin opens the bell. */
    public function markRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $user = auth()->user();

        $messageNotifs = $user
            ? $user->notifications()->where('type', MessageReceived::class)->latest()->take(5)->get()
            : collect();
        $bookingNotifs = $user
            ? $user->notifications()->where('type', BookingReceived::class)->latest()->take(6)->get()
            : collect();
        $notifCount = $user ? $user->unreadNotifications()->count() : 0;

        return view('admin.notifications.bell', compact(
            'messageNotifs',
            'bookingNotifs',
            'notifCount',
        ));
    }
}
