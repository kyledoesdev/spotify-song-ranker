<?php

namespace App\QueryBuilders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RankingQueryBuilder extends Builder
{
    public function forExplorePage(array $filters): static
    {
        return $this->newQuery()
            ->public()
            ->completed()
            ->when(isset($filters['search']), function (Builder $query) use ($filters) {
                $query->where(fn (Builder $query) => $query
                    ->whereHas('artist', fn (Builder $query) => $query->where('artist_name', 'LIKE', "%{$filters['search']}%"))
                    ->orWhereHas('playlist', fn (Builder $query) => $query->where('name', 'LIKE', "%{$filters['search']}%"))
                );
            })
            ->when(isset($filters['artist']), fn (Builder $query) => $query
                ->whereHas('artist', fn (Builder $query) => $query->where('id', $filters['artist']))
            )
            ->when(isset($filters['playlist']), fn (Builder $query) => $query
                ->whereHas('playlist', fn (Builder $query) => $query->where('id', $filters['playlist']))
            )
            ->withHasPodcastEpisode()
            ->with('user', 'artist', 'playlist')
            ->with('songs', fn ($query) => $query->where('rank', 1))
            ->withCount('songs')
            ->orderBy('completed_at', 'desc');
    }

    public function forProfilePage(User $user): static
    {
        return $this->newQuery()
            ->where('user_id', $user ? $user->getKey() : auth()->id())
            ->when($user && $user->getKey() !== auth()->id(), fn (Builder $query) => $query->public()->completed())
            ->withHasPodcastEpisode()
            ->with('user', 'artist')
            ->with('songs', fn ($q) => $q->where('rank', 1))
            ->withCount('songs')
            ->orderByRaw('completed_at IS NULL DESC, completed_at DESC');
    }

    public function forReminders(): static
    {
        return $this->newQuery()
            ->select('id', 'user_id', 'artist_id', 'name', 'is_ranked', 'created_at')
            ->private()
            ->with('artist')
            ->withCount('songs');
    }

    public function forNewsletter(): static
    {
        return $this->newQuery()
            ->public()
            ->completed()
            ->whereBetween('completed_at', [now()->subMonth(), now()])
            ->withHasPodcastEpisode()
            ->with('user', 'artist')
            ->with('songs', fn (Builder $query) => $query->where('rank', 1))
            ->withCount('songs')
            ->orderBy('completed_at', 'desc');
    }

    public function withHasPodcastEpisode()
    {
        return $this->addSelect([
            'has_podcast_episode' => function($q) {
                $q->selectRaw('CASE WHEN EXISTS (
                    SELECT 1 FROM songs 
                    INNER JOIN artists ON songs.artist_id = artists.id 
                    WHERE songs.ranking_id = rankings.id 
                    AND artists.is_podcast = 1
                ) THEN 1 ELSE 0 END');
            }
        ]);
    }

    public function public(): static
    {
        return $this->where('is_public', true);
    }

    public function private(): static
    {
        return $this->where('is_public', false);
    }

    public function completed(): static
    {
        return $this->where('is_ranked', true);
    }
}
