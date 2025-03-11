<?php

namespace App\Console\Commands;

use App\Http\Controllers\SpotifyAPIController;
use App\Models\Artist;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateArtistImages extends Command
{
    protected $signature = 'artists:update-images';

    protected $description = 'Update Artist Profile Images';

    public function handle()
    {
        $authenticated = $this->authenticateForSpotify();

        if (! $authenticated) {
            return 1;
        }

        Artist::all()->chunk(50)->each(function($chunk) {
            $ids = $chunk->pluck('artist_id')->implode(',');

            $response = (new Client)->request(
                "GET",
                "https://api.spotify.com/v1/artists?ids={$ids}", [
                    'headers' => [
                        'Authorization' => 'Bearer '. auth()->user()->external_token,
                    ]
                ]
            );

            $artists = collect(collect(json_decode($response->getBody()))->get('artists'));

            if (count($artists)) {
                $artists = collect($artists->map(fn ($artist) => [
                    'artist_id' => $artist->id,
                    'artist_img' => $artist->images[0]->url
                ]));
                
                foreach ($artists as $artist) {
                    Artist::query()
                        ->where('artist_id', $artist['artist_id'])
                        ->first()
                        ->update(['artist_img' => $artist['artist_img']]);
                }
            }
        });

        Auth::logout();

        return 0;
    }

    private function authenticateForSpotify(): bool
    {
        try {
            Auth::login(User::where('spotify_id', env("SYSTEM_SPOTIFY_ID"))->firstOrFail());
            (new SpotifyAPIController)->refreshToken();
            return true;
        } catch(Exception $e) {
            Log::error("Could not authenticate with spotify for updating artist images.");
            return false;
        }
    }
}
