<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
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
            'notifications' => Auth::user()->notifications()->paginate(10),
        ]);
    }
}
