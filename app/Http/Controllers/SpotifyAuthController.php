<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SpotifyAuthController extends Controller {
    public function login() {
        return Socialite::driver('spotify')
            ->scopes(['user-read-email'])
            ->redirect();
    }

    public function processLogin() {
        $user = Socialite::driver('spotify')->user();

        $user = User::updateOrCreate([
            'spotify_id' => $user->id
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'external_token' => $user->token,
            'external_refresh_token' => $user->refreshToken
        ]);

        Auth::login($user);

        return redirect(route('home'));
    }
}
