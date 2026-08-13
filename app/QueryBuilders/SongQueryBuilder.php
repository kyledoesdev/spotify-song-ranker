<?php

namespace App\QueryBuilders;

use App\Enums\RankingType;
use Illuminate\Database\Eloquent\Builder;

class SongQueryBuilder extends Builder
{
    public function rankedArtistCount(): int
    {
        return (int) (round($this->newQuery()
            ->whereHas('ranking', fn (Builder $query) => $query->completed()->where('type', RankingType::ARTIST->value))
            ->where('featured_artist', false)
            ->distinct('artist_id')
            ->count() / 25) * 25);
    }
}
