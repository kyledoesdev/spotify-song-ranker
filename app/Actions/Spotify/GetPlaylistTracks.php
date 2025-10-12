<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GetPlaylistTracks
{
    public function search(User $user, string $playlistUrl): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        $offset = 0;
        $tracks = collect();
        $playlistId = $this->pluckPlaylistId($playlistUrl);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$user->external_token,
                'Content-Type' => 'application/json',
            ])->get("https://api.spotify.com/v1/playlists/{$playlistId}", [
                'limit' => 100,
                'offset' => 0,
            ]);

            if ($response->status() === 400) {
                return null;
            }

            $total = $response->json('tracks.total');

            for ($i = 0; $i < round($total / 100, 2, PHP_ROUND_HALF_UP); $i++) {
                $subResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$user->external_token,
                    'Content-Type' => 'application/json',
                ])->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                    'limit' => 100,
                    'offset' => $offset,
                ]);

                collect(collect($subResponse->json('items'))->pluck('track'))->map(function (array $track) use ($tracks) {
                    $tracks->push([
                        'id' => $track['id'],
                        'name' => $track['name'],
                        'cover' => data_get($track, 'album.images.0.url'),
                        'artist_id' => data_get($track, 'artists.0.id'),
                        'artist_name' => data_get($track, 'artists.0.name') ?? data_get($track, 'artists.0.type'),
                        'is_podcast' => data_get($track, 'artists.0.type') !== 'artist',
                    ]);
                });

                $offset += 100;
            }
        } catch (Exception $e) {
            report($e);

            return null;
        }

        return collect([
            'id' => $response->json('id'),
            'name' => $response->json('name'),
            'description' => $response->json('description'),
            'creator' => $response->json('owner'),
            'cover' => $response->json('images.0.url'),
            'track_count' => $total,
            'tracks' => $tracks,
        ]);
    }

    private function pluckPlaylistId(string $playlist): string
    {
        return Str::after(Str::before($playlist, '?'), '/playlist/');
    }
}
