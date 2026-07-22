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

    /**
     * The artist credit under the track name — the primary artist on an "appears on"
     * track in an artist ranking, otherwise the artist the track arrived with
     * (playlist tracks, show episodes). Owned artist tracks carry neither key,
     * since the ranked artist is implied.
     */
    public function primaryArtistName(): ?string
    {
        return data_get($this->song, 'primary_artist.name');
    }
}
