<?php

namespace App\QueryBuilders;

use App\Enums\RankingType;
use Illuminate\Database\Eloquent\Builder;

class PlaylistQueryBuilder extends Builder
{
    public function topPlaylists(): static
    {
        return $this->newQuery()
            ->selectRaw('
                count(rankings.source_id) as playlist_rankings_count,
                playlists.id,
                playlists.playlist_id,
                playlists.name,
                playlists.cover,
                playlists.creator_name
            ')
            ->join('rankings', function ($join) {
                $join->on('rankings.source_id', '=', 'playlists.id')
                    ->where('rankings.type', RankingType::PLAYLIST->value)
                    ->whereNull('rankings.deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->groupBy('rankings.source_id')
            ->orderBy('playlist_rankings_count', 'desc')
            ->orderBy('playlists.name', 'asc');
    }

    public function rankedPlaylistCount(): int
    {
        return (int) (round($this->newQuery()
            ->whereHas('rankings', fn (Builder $query) => $query->completed()->public())
            ->distinct('playlist_id')
            ->count() / 25) * 25);
    }
}
