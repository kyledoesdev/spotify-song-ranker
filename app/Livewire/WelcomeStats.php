<?php

namespace App\Livewire;

use Livewire\Component;

class WelcomeStats extends Component
{
    public int $users;
    public int $rankings;
    public int $artists;

    public function render()
    {
        return view('livewire.welcome-stats');
    }
}
