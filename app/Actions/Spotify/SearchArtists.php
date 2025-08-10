<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

final class SearchArtists
{
    public function handle(User $user, string $searchTerm): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$user->external_token,
                'Content-Type' => 'application/json',
            ])->get('https://api.spotify.com/v1/search', [
                'q' => $searchTerm,
                'type' => 'artist',
                'limit' => 10,
            ]);

            $data = collect($response->json('artists.items'));

            $artists = collect();

            $data->each(function ($artist) use ($artists) {
                if ($artist['images'] && $artist['images'][0] && $artist['images'][0]['url']) {
                    $artists->push([
                        'id' => $artist['id'],
                        'name' => $artist['name'],
                        'cover' => $artist['images'][0]['url'],
                    ]);
                }
            });
        } catch (Exception $e) {
            report($e);

            return null;
        }

        return $artists;
    }
}
