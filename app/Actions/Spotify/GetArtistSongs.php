<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GetArtistSongs
{
    /**
     * The artist's own catalog. Tracks they are featured on load separately
     * through GetArtistAppearsOnSongs, only when the user asks for them.
     *
     * @return Collection<int, array>|null
     */
    public function handle(User $user, string $artistId): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        try {
            $response = $this->get($user, "https://api.spotify.com/v1/artists/{$artistId}/albums", [
                'include_groups' => 'album,single',
                'limit' => 50,
            ]);

            $totalAlbumCount = (int) $response->json('total');
            $songs = collect();
            $offset = 0;

            for ($i = 0; $i < ceil($totalAlbumCount / 50); $i++) {
                $response = $this->get($user, "https://api.spotify.com/v1/artists/{$artistId}/albums", [
                    'include_groups' => 'album,single',
                    'limit' => 50,
                    'offset' => $offset,
                ]);

                $albumIds = collect($response->json('items'))->pluck('id');

                foreach ($albumIds->chunk(20) as $chunk) {
                    $albums = collect($this->get($user, 'https://api.spotify.com/v1/albums', [
                        'ids' => $chunk->implode(','),
                    ])->json('albums'))->filter();

                    foreach ($albums as $album) {
                        /* the batch album endpoint only embeds the first page of tracks */
                        $albumTracks = collect(data_get($album, 'tracks.items', []));

                        for ($trackOffset = $albumTracks->count(); $trackOffset < (int) data_get($album, 'tracks.total', 0); $trackOffset += 50) {
                            $albumTracks = $albumTracks->concat($this->get($user, "https://api.spotify.com/v1/albums/{$album['id']}/tracks", [
                                'limit' => 50,
                                'offset' => $trackOffset,
                            ])->json('items') ?? []);
                        }

                        foreach ($albumTracks as $track) {
                            $songs->push([
                                'id' => $track['id'],
                                'name' => (string) $track['name'],
                                'uuid' => (string) Str::uuid(),
                                'cover' => data_get($album, 'images.0.url'),
                            ]);
                        }
                    }
                }

                $offset += 50;
            }

            return $songs->unique('name')->values();
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    private function get(User $user, string $url, array $query = []): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$user->external_token,
            'Content-Type' => 'application/json',
        ])->get($url, $query);
    }
}
