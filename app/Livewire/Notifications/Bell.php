<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Bell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->unreadCount = Auth::user()->unreadNotifications()->count();
    }

    public function open(): void
    {
        if ($this->unreadCount > 0) {
            Auth::user()->unreadNotifications->markAsRead();

            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.notifications.bell', [
            'notifications' => Auth::user()
                ->notifications()
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }
}
