<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class ArtistQueryBuilder extends Builder
{
    public function topArtists(int $limit = 10): static
    {
        return $this->newQuery()
            ->selectRaw('
                count(rankings.artist_id) as artist_rankings_count,
                artists.id,
                artists.artist_id,
                artists.artist_name,
                artists.artist_img
            ')
            ->join('rankings', function ($join) {
                $join->on('rankings.artist_id', '=', 'artists.id')
                    ->whereNull('rankings.deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->whereNotNull('artists.artist_img')
            ->groupBy('rankings.artist_id')
            ->orderBy('artist_rankings_count', 'desc')
            ->orderBy('artists.artist_name', 'asc')
            ->limit($limit);
    }
}
