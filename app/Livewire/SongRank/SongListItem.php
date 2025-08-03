<?php

namespace App\Livewire\SongRank;

use Livewire\Component;

class SongListItem extends Component
{
    public array $song = [];

    public bool $canDelete = true;

    public function render()
    {
        return view('livewire.song-rank.song-list-item');
    }

    public function removeSong(string $songId)
    {
        $this->dispatch('song-removed', id: $songId);
    }
}
