<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class PlaylistQueryBuilder extends Builder
{
    public function topPlaylists(): static
    {
        return $this->newQuery()
            ->selectRaw('
                count(rankings.playlist_id) as playlist_rankings_count,
                playlists.id,
                playlists.playlist_id,
                playlists.name,
                playlists.cover,
                playlists.creator_name
            ')
            ->join('rankings', function ($join) {
                $join->on('rankings.playlist_id', '=', 'playlists.id')
                    ->whereNull('rankings.deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->groupBy('rankings.playlist_id')
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
