<?php

namespace App\Actions\Rankings;

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class StoreArtistRanking
{
    public function handle(User $user, array $attributes): Ranking
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
                'comments_enabled' => $attributes['comments_enabled'] ?? false,
                'comments_replies_enabled' => $attributes['comments_replies_enabled'] ?? false,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            $tracks = collect($attributes['tracks']);

            /* "appears on" tracks belong to their primary artist, so those records need to exist.
               They arrive without artwork; UpdateArtistImages backfills it. */
            $primaryArtists = $tracks
                ->filter(fn (array $song) => $song['featured_artist'] ?? false)
                ->pluck('primary_artist')
                ->unique('id')
                ->values();

            Artist::upsert(
                $primaryArtists->map(fn (array $primary) => [
                    'artist_id' => $primary['id'],
                    'artist_name' => $primary['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all(),
                ['artist_id'],
                ['artist_name', 'updated_at']
            );

            $primaryArtistKeys = Artist::query()
                ->whereIn('artist_id', $primaryArtists->pluck('id'))
                ->pluck('id', 'artist_id');

            $songs = $tracks->map(function ($song) use ($ranking, $artist, $primaryArtistKeys) {
                $isFeaturedTrack = $song['featured_artist'] ?? false;

                return [
                    'ranking_id' => $ranking->getKey(),
                    'artist_id' => $isFeaturedTrack
                        ? $primaryArtistKeys->get($song['primary_artist']['id'])
                        : $artist->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => $song['uuid'],
                    'title' => $song['name'] ?? 'Track deleted from spotify servers.',
                    'cover' => $song['cover'] ?? 'https://i.imgur.com/MBDmIUg.png',
                    'featured_artist' => $isFeaturedTrack,
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
