<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SpotifyAuthController extends Controller 
{
    public function login() 
    {
        return Socialite::driver('spotify')
            ->scopes(['user-read-email'])
            ->redirect();
    }

    public function processLogin()
    {
        $user = Socialite::driver('spotify')->user();

        $user = User::updateOrCreate([
            'spotify_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$user->name}",
            'external_token' => $user->token,
            'external_refresh_token' => $user->refreshToken,
        ]);

        Log::warning($user->email . ' just logged in!!');

        Auth::login($user);

        return redirect(route('home'));
    }
}
