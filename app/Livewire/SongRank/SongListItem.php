<?php

namespace App\Livewire\SongRank;

use App\Enums\RankingType;
use Livewire\Component;

class SongListItem extends Component
{
    public RankingType $type;

    public array $song = [];

    public function render()
    {
        return view('livewire.song-rank.song-list-item');
    }

    public function removeSong(string $uuid)
    {
        $this->dispatch('song-removed', uuid: $uuid, type: $this->type);
    }
}
