<?php

namespace App\Livewire;

use App\Models\Ranking as RankingModel;
use Livewire\Component;

class Ranking extends Component
{
    public RankingModel $ranking;

    public function render()
    {
        return view('livewire.ranking');
    }
}
