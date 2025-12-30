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

            if ($total > 500) {
                return null;
            }

            for ($i = 0; $i < ceil($total / 100); $i++) {
                $subResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$user->external_token,
                    'Content-Type' => 'application/json',
                ])->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                    'limit' => 100,
                    'offset' => $offset,
                ]);

                $deletedTracks = [];

                for ($i = 0; $i < ceil($total / 100); $i++) {
                    $subResponse = Http::withHeaders([
                        'Authorization' => 'Bearer '.$user->external_token,
                        'Content-Type' => 'application/json',
                    ])->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                        'limit' => 100,
                        'offset' => $offset,
                    ]);

                    collect($subResponse->json('items'))
                        ->pluck('track')
                        ->filter()
                        ->each(function (array $track) use ($tracks, &$deletedTracks) {
                            if (empty(data_get($track, 'artists.0.id'))) {
                                $artistName = data_get($track, 'artists.0.name') ?? 'Unknown';
                                $trackName = $track['name'] ?? 'Unknown';

                                $deletedTracks[] = !is_null($artistName) ? ($artistName . ' - ' . $trackName) : $trackName;
                                return;
                            }

                            $tracks->push([
                                'id' => $track['id'],
                                'name' => (string) $track['name'],
                                'uuid' => (string) Str::uuid(),
                                'cover' => data_get($track, 'album.images.0.url'),
                                'artist_id' => data_get($track, 'artists.0.id'),
                                'artist_name' => data_get($track, 'artists.0.name') ?? data_get($track, 'artists.0.type'),
                                'is_podcast' => data_get($track, 'artists.0.type') !== 'artist',
                            ]);
                        });

                    $offset += 100;
                }
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
            'deleted_tracks' => ! empty($deletedTracks) ? $deletedTracks : null,
        ]);
    }

    private function pluckPlaylistId(string $playlist): string
    {
        return Str::after(Str::before($playlist, '?'), '/playlist/');
    }
}
