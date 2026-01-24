<?php

namespace App\Actions\Rankings;

use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class StorePlaylistRanking
{
    public function handle(User $user, array $attributes): Ranking
    {
        return DB::transaction(function () use ($user, $attributes) {
            $playlist = Playlist::updateOrCreate([
                'playlist_id' => data_get($attributes, 'playlist.id'),
            ], [
                'creator_id' => data_get($attributes, 'playlist.creator.id'),
                'creator_name' => data_get($attributes, 'playlist.creator.display_name'),
                'name' => data_get($attributes, 'playlist.name'),
                'description' => data_get($attributes, 'playlist.description'),
                'cover' => data_get($attributes, 'playlist.cover'),
                'track_count' => data_get($attributes, 'playlist.track_count'),
            ]);

            $name = $attributes['ranking_name'] === '' || is_null($attributes['ranking_name'])
                ? $playlist->name.' List'
                : $attributes['ranking_name'];

            $ranking = Ranking::create([
                'playlist_id' => $playlist->getKey(),
                'user_id' => $user->getKey(),
                'name' => Str::limit($name, 30),
                'is_public' => $attributes['is_public'] ?? false,
                'comments_enabled' => $attributes['comments_enabled'] ?? false,
                'comments_replies_enabled' => $attributes['comments_enabled'] ?? false,
            ]);

            $ranking->sortingState()->create();

            $tracks = collect($attributes['tracks']);

            // Upsert all unique artists in one query
            $uniqueArtists = $tracks
                ->unique('artist_id')
                ->map(fn ($track) => [
                    'artist_id' => $track['artist_id'],
                    'artist_name' => $track['artist_name'],
                    'is_podcast' => $track['is_podcast'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->values()
                ->toArray();

            Artist::upsert($uniqueArtists, ['artist_id'], ['artist_name', 'is_podcast', 'updated_at']);

            // Fetch all artists we need in one query
            $artists = Artist::query()
                ->whereIn('artist_id', $tracks->pluck('artist_id')->unique())
                ->get()
                ->keyBy('artist_id');

            // Map songs with artist IDs
            $songs = $tracks->map(fn ($track) => [
                'artist_id' => $artists->get($track['artist_id'])?->getKey(),
                'ranking_id' => $ranking->getKey(),
                'spotify_song_id' => $track['id'],
                'uuid' => $track['uuid'],
                'title' => $track['name'] ?? 'Track deleted from spotify servers.',
                'cover' => $track['cover'] ?? 'https://i.imgur.com/MBDmIUg.png',
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            Song::insert($songs);

            return $ranking;
        });
    }
}