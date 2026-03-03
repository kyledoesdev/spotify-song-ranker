<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class Details extends Component
{
    public bool $open = false;

    public ?Notification $notification = null;

    #[On('show-notification-details')]
    public function show(string $notificationId): void
    {
        $this->notification = Notification::query()
            ->where('id', $notificationId)
            ->where('notifiable_id', auth()->id())
            ->firstOrFail();

        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->notification = null;
    }

    public function render()
    {
        return view('livewire.notifications.details');
    }
}
