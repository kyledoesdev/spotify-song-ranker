<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

        $playlistId = $this->pluckPlaylistId($playlistUrl);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$user->external_token,
            'Content-Type' => 'application/json',
        ])->get("https://api.spotify.com/v1/playlists/{$playlistId}", [
            'limit' => 100,
            'offset' => 0
        ]);

        $owner = $response->json('owner');
        $total = $response->json('tracks.total');
        $offset = 0;

        $tracks = collect();

        for ($i = 0; $i < round($total / 100); $i++) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$user->external_token,
                'Content-Type' => 'application/json',
            ])->get("https://api.spotify.com/v1/playlists/{$playlistId}", [
                'limit' => 100,
                'offset' => $offset
            ]);

            collect(collect($response->json('tracks.items'))->pluck('track'))->map(function(array $track) use ($tracks) {
                $tracks->push([
                    'artist' => $track['artists'][0],
                    'track' => [
                        'id' => $track['id'],
                        'name' => $track['name'],
                        'cover' => $track['album']['images'][0]['url']
                    ],
                ]);
            });

            $offset += 100;
        }

        dd($tracks->take(5));

        return collect();
    }

    private function pluckPlaylistId(string $playlist): string
    {
        return Str::after(Str::before($playlist, '?'), '/playlist/');
    }
}