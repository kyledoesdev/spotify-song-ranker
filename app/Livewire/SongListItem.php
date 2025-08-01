<?php

namespace App\Livewire;

use Livewire\Component;

class SongListItem extends Component
{
    public array $song = [];
    public bool $canDelete = true;

    public function render()
    {
        return view('livewire.song-list-item');
    }

    public function removeSong(string $songId)
    {
        $this->dispatch('song-removed', id: $songId);
    }
}
