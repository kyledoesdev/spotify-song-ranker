<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GetShowEpisodes
{
    public function search(User $user, string $url): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        $showId = $this->pluckShowId($url);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$user->external_token,
                'Content-Type' => 'application/json',
            ])->get("https://api.spotify.com/v1/shows/{$showId}");

            if ($response->status() === 400) {
                return null;
            }

            $total = $response->json('total_episodes', 0);

            if ($total > 500) {
                return null;
            }

            $publisher = $response->json('publisher', 'Unknown');
            $publisherImage = $response->json('images.0.url');

            $offset = 0;
            $tracks = collect();

            for ($i = 0; $i < ceil($total / 50); $i++) {
                $subResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$user->external_token,
                    'Content-Type' => 'application/json',
                ])->get("https://api.spotify.com/v1/shows/{$showId}/episodes", [
                    'limit' => 50,
                    'offset' => $offset,
                ]);

                collect($subResponse->json('items'))
                    ->filter()
                    ->each(function (array $item) use ($tracks, $publisher, $showId) {
                        $tracks->push([
                            'id' => $item['id'],
                            'name' => (string) $item['name'],
                            'uuid' => (string) Str::uuid(),
                            'cover' => data_get($item, 'images.0.url'),
                            'artist_id' => $showId,
                            'artist_name' => $publisher,
                            'is_podcast' => true,
                        ]);
                    });

                $offset += 50;
            }
        } catch (Exception $e) {
            report($e);

            return null;
        }

        return collect([
            'id' => $response->json('id'),
            'name' => $response->json('name'),
            'description' => $response->json('description'),
            'publisher' => $publisher,
            'publisher_image' => $publisherImage,
            'cover' => $response->json('images.0.url'),
            'episode_count' => $total,
            'tracks' => $tracks,
            'data' => [
                'media_type' => $response->json('media_type'),
                'explicit' => $response->json('explicit'),
                'languages' => $response->json('languages'),
            ],
        ]);
    }

    private function pluckShowId(string $url): string
    {
        return Str::after(Str::before($url, '?'), '/show/');
    }
}
