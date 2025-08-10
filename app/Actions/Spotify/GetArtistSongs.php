<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

final class GetArtistSongs
{
    public function handle(User $user, string $artistId): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$user->external_token,
                'Content-Type' => 'application/json',
            ])->get("https://api.spotify.com/v1/artists/{$artistId}/albums", [
                'include_groups' => 'album,single',
                'limit' => 50,
            ]);

            $totalAlbumCount = $response->json('total');
            $songs = collect();
            $offset = 0;

            for ($i = 0; $i < round($totalAlbumCount / 50, 2, PHP_ROUND_HALF_UP); $i++) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$user->external_token,
                    'Content-Type' => 'application/json',
                ])->get("https://api.spotify.com/v1/artists/{$artistId}/albums", [
                    'include_groups' => 'album,single',
                    'limit' => 50,
                    'offset' => $offset,
                ]);

                $albums = collect($response->json('items'))->pluck('id');
                $albumRequests = array_chunk($albums->toArray(), 20);

                foreach ($albumRequests as $albumRequest) {
                    $albumIds = implode(',', $albumRequest);

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$user->external_token,
                        'Content-Type' => 'application/json',
                    ])->get('https://api.spotify.com/v1/albums', [
                        'ids' => $albumIds,
                    ]);

                    $albums = collect($response->json('albums'));

                    $albums->each(function ($album) use ($songs) {
                        $albumSongs = collect($album['tracks']['items']);

                        $albumSongs->each(function ($song) use ($album, $songs) {
                            $songs->push([
                                'id' => $song['id'],
                                'name' => $song['name'],
                                'cover' => $album['images'][0]['url'],
                            ]);
                        });
                    });
                }

                $offset += 50;
            }

            $songs = $songs->groupBy('name')->map->first();

        } catch (Exception $e) {
            report($e);

            return null;
        }

        return $songs;
    }
}
