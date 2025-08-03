<?php

namespace App\Livewire\Welcome;

use Livewire\Component;

class Stats extends Component
{
    public int $users;

    public int $rankings;

    public int $artists;

    public function render()
    {
        return view('livewire.welcome.stats');
    }
}
