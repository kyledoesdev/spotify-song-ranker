<?php

namespace App\Actions\Rankings;

use App\Enums\RankingType;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class StoreRanking
{
    public function handle(User $user, RankingType $type, array $attributes): Ranking
    {
        return match ($type) {
            RankingType::ARTIST => $this->artist($user, $attributes),
            RankingType::PLAYLIST => $this->playlist($user, $attributes),
            default => null,
        };
    }

    private function artist(User $user, array $attributes): Ranking
    {
        return DB::transaction(function () use ($user, $attributes) {
            /* update or create the artist */
            $artist = Artist::updateOrCreate([
                'artist_id' => data_get($attributes, 'artist.id'),
            ], [
                'artist_name' => data_get($attributes, 'artist.name'),
                'artist_img' => data_get($attributes, 'artist.cover'),
            ]);

            /* update or create the artist */
            $name = $attributes['ranking_name'] === '' || is_null($attributes['ranking_name'])
                ? $artist->artist_name.' List'
                : $attributes['ranking_name'];

            /* create a new ranking */
            $ranking = Ranking::create([
                'artist_id' => $artist->getKey(),
                'user_id' => $user->getKey(),
                'name' => Str::limit($name, 30),
                'is_public' => $attributes['is_public'] ?? false,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            $songs = collect($attributes['tracks'])->map(function ($song) use ($ranking, $artist) {
                return [
                    'ranking_id' => $ranking->getKey(),
                    'artist_id' => $artist->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => Str::uuid(),
                    'title' => $song['name'] ?? 'Track deleted from spotify servers.',
                    'cover' => $song['cover'] ?? 'https://i.imgur.com/MBDmIUg.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            /* batch insert the song records */
            Song::insert($songs);

            return $ranking;
        });
    }

    private function playlist(User $user, array $attributes): Ranking
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

            /* update or create the artist */
            $name = $attributes['ranking_name'] === '' || is_null($attributes['ranking_name'])
                ? $playlist->name.' List'
                : $attributes['ranking_name'];

            /* create a new ranking */
            $ranking = Ranking::create([
                'playlist_id' => $playlist->getKey(),
                'user_id' => $user->getKey(),
                'name' => Str::limit($name, 30),
                'is_public' => $attributes['is_public'] ?? false,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            /* get the artists for each track if we already have them */
            $artists = Artist::query()
                ->whereIn('artist_id', collect($attributes['tracks'])->pluck('artist_id')->unique())
                ->get()
                ->keyBy('artist_id');

            /* map through the songs, assign the artist or create a record of one. */
            $songs = collect($attributes['tracks'])->map(function ($song) use ($artists, $ranking) {
                if (is_null($song['artist_id'])) {
                    Log::channel('discord_other_updates')->error('Artist Id not set on Song: ' . $song['name'] . ' ' . $song['artist_name']);
                }

                $artist = $artists->get($song['artist_id'])
                    ?? Artist::updateOrCreate([
                        'artist_id' => $song['artist_id'],
                    ], [
                        'artist_id' => $song['artist_id'],
                        'artist_name' => $song['artist_name'],
                        'is_podcast' => $song['is_podcast'],
                    ]);

                return [
                    'artist_id' => $artist->getKey(),
                    'ranking_id' => $ranking->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => Str::uuid(),
                    'title' => $song['name'] ?? 'Track deleted from spotify servers.',
                    'cover' => $song['cover'] ?? 'https://i.imgur.com/MBDmIUg.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            /* batch insert the song records */
            Song::insert($songs);

            return $ranking;
        });
    }
}
