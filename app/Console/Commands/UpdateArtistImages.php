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

    private int $updated = 0;

    public function handle()
    {
        $loggedIn = $this->login();

        if (! $loggedIn) {
            return Command::FAILURE;
        }

        Artist::query()
            ->chunk(50, function ($chunk) {
                $ids = $chunk->pluck('artist_id')->implode(',');

                $response = Http::withToken(Auth::user()->external_token)
                    ->get("https://api.spotify.com/v1/artists?ids={$ids}");

                $artists = collect($response->json('artists'))
                    ->filter()
                    ->keyBy('id');

                $chunk->each(function (Artist $artist) use ($artists) {
                    $data = $artists->get($artist->artist_id);

                    if (! $data) {
                        return;
                    }

                    $image = data_get($data, 'images.0.url');

                    /* spotify hands back the same artwork on almost every run */
                    if ($artist->artist_img === $image) {
                        return;
                    }

                    $artist->update([
                        'artist_name' => $data['name'],
                        'artist_img' => $image,
                    ]);

                    $this->updated++;
                });
            });

        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        Log::channel('discord_other_updates')->info("Updated {$this->updated} artist images.");

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
