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
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(fn (Builder $query) => $query
                    ->whereIn('artist_id', fn ($q) => $q
                        ->select('id')
                        ->from('artists')
                        ->where('artist_name', 'LIKE', "%{$search}%")
                    )
                    ->orWhereIn('playlist_id', fn ($q) => $q
                        ->select('id')
                        ->from('playlists')
                        ->where('name', 'LIKE', "%{$search}%")
                    )
                );
            })
            ->when($filters['artist'] ?? null, fn (Builder $query, string $artist) => $query
                ->where('artist_id', $artist)
            )
            ->when($filters['playlist'] ?? null, fn (Builder $query, string $playlist) => $query
                ->where('playlist_id', $playlist)
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
            ->with('songs', fn ($query) => $query->with('artist'))
            ->withCount('songs')
            ->orderByRaw('completed_at IS NULL DESC, completed_at DESC');
    }

    public function forReminders(): static
    {
        return $this->newQuery()
            ->select('id', 'user_id', 'artist_id', 'name', 'is_ranked', 'created_at')
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
            ->with('songs', fn ($query) => $query->where('rank', 1))
            ->withCount('songs')
            ->orderBy('completed_at', 'desc');
    }

    public function withHasPodcastEpisode()
    {
        return $this->addSelect([
            'has_podcast_episode' => function ($q) {
                $q->selectRaw('CASE WHEN EXISTS (
                    SELECT 1 FROM songs 
                    INNER JOIN artists ON songs.artist_id = artists.id 
                    WHERE songs.ranking_id = rankings.id 
                    AND artists.is_podcast = 1
                ) THEN 1 ELSE 0 END');
            },
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
