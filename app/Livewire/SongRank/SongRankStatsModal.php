<?php

namespace App\Livewire\SongRank;

use App\Models\Ranking;
use App\Models\RankingSortingState;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SongRankStatsModal extends Component
{
    public Ranking $ranking;

    public RankingSortingState $sortingState;

    public Collection $songs;

    public int $refreshKey = 0;

    public function refreshData(): void
    {
        $this->refreshKey++;
    }

    public function render()
    {
        return view('livewire.song-rank.song-rank-stats-modal');
    }
}