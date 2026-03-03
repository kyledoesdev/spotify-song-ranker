<?php

namespace App\Livewire\Notifications;

use Livewire\Component;

class Bell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function open(): void
    {
        if ($this->unreadCount > 0) {
            auth()->user()->unreadNotifications->markAsRead();
            
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.notifications.bell', [
            'notifications' => auth()->user()
                ->notifications()
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }
}
