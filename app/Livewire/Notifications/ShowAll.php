<?php

namespace App\Livewire\Notifications;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('songrank.dev - My Notifications')]
class ShowAll extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.notifications.show-all', [
            'notifications' => auth()->user()->notifications()->paginate(10),
        ]);
    }
}
