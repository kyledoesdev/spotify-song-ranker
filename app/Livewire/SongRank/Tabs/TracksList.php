<?php

namespace App\Livewire\SongRank\Tabs;

use App\Models\Ranking;
use Livewire\Component;

class TracksList extends Component
{
    public Ranking $ranking;

    public function render()
    {
        return view('livewire.song-rank.tabs.tracks-list', [
            'songs' => $this->ranking->songs->sortBy('title'),
            'ranking' => $this->ranking
        ]);
    }
}