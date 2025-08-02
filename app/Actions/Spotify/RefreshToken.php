<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

final class RefreshToken
{
    public function handle(User $user): bool
    {
        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $user->external_refresh_token,
                'client_id' => config('services.spotify.client_id'),
                'client_secret' => config('services.spotify.client_secret'),
            ]);

            $data = $response->json();

            $user->update([
                'external_token' => $data['access_token'],
            ]);

            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}