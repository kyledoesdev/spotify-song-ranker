<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class SongQueryBuilder extends Builder
{
    public function rankedArtistCount(): int
    {
        return (int) (round($this->newQuery()
            ->whereHas('ranking', fn (Builder $query) => $query->completed()->whereNull('playlist_id')->whereNull('show_id'))
            ->where('featured_artist', false)
            ->distinct('artist_id')
            ->count() / 25) * 25);
    }
}
