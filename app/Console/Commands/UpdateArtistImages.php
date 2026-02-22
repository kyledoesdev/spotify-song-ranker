<?php

namespace App\Console\Commands;

use App\Actions\Spotify\RefreshToken;
use App\Models\Artist;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UpdateArtistImages extends Command
{
    protected $signature = 'artists:update-images';

    protected $description = 'Update Artist Profile Images';

    public function handle()
    {
        $loggedIn = $this->login();

        if (! $loggedIn) {
            return Command::FAILURE;
        }

        /* Loop through every artist in chunks of 50 and attempt to update their image. */
        Artist::query()->chunk(50, function ($chunk) {
            $ids = $chunk->pluck('artist_id')->implode(',');

            $response = Http::withToken(auth()->user()->external_token)
                ->get("https://api.spotify.com/v1/artists?ids={$ids}");

            $upserts = collect($response->json('artists'))
                ->filter()
                ->map(fn ($artist) => [
                    'artist_id' => $artist['id'],
                    'artist_name' => $artist['name'],
                    'artist_img' => data_get($artist, 'images.0.url'),
                ])
                ->all();

            if ($upserts) {
                Artist::query()->upsert($upserts, ['artist_id'], ['artist_name', 'artist_img']);
            }
        });

        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return Command::SUCCESS;
    }

    private function login(): bool
    {
        try {
            $user = User::where('spotify_id', config('services.spotify.system_id'))->firstOrFail();

            Auth::login($user);

            Session::regenerate();

            $success = (new RefreshToken)->handle($user);

            return $success;
        } catch (Exception) {
            Log::channel('discord_other_updates')->error('Could not authenticate with spotify for updating artist images.');

            return false;
        }
    }
}
