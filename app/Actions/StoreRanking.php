<?php

namespace App\Actions;

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class StoreRanking
{
    public function handle(User $user, array $attributes): Ranking
    {
        return DB::transaction(function () use ($user, $attributes) {
            /* update or create the artist */
            $artist = Artist::updateOrCreate([
                'artist_id' => $attributes['artist_id'],
            ], [
                'artist_name' => $attributes['artist_name'],
                'artist_img' => $attributes['artist_img'],
            ]);

            $name = $attributes['ranking_name'] === '' || is_null($attributes['ranking_name'])
                ? $artist->artist_name . ' List'
                : $attributes['ranking_name'];

            /* create a new ranking */
            $ranking = Ranking::create([
                'user_id' => $user->getKey(),
                'artist_id' => $artist->getKey(),
                'name' => Str::limit($name, 30),
                'is_public' => $attributes['is_public'] ?? false,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            $songs = [];
            foreach ($attributes['tracks'] as $song) {
                array_push($songs, [
                    'ranking_id' => $ranking->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => Str::uuid(),
                    'title' => $song['name'] ?? 'Track deleted from spotify servers.',
                    'cover' => $song['cover'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /* batch insert the song records */
            Song::insert($songs);

            return $ranking;
        });
    }
}