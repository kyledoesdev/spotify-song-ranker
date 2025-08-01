<?php

namespace App\Livewire;

use App\Models\Ranking;
use Livewire\Component;

class SongRanking extends Component
{
    public Ranking $ranking;

    public function render()
    {
        return view('livewire.ranking');
    }
}
