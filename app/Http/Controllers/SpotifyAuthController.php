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

        $deletedUser = User::withTrashed()
            ->where('spotify_id', $user->id)
            ->whereNotNull('deleted_at')
            ->first();

        if (!is_null($deletedUser)) {
            Log::channel('discord')->warning($user->name . ' is back from the dead!!!!');
            $deletedUser->restore();
            session()->flash('success', "Welcome back {$user->name}.. we've been expecting you.. To revive your rankings - create an issue on our github page. (Link in the footer of the site.)");
        }

        $user = User::withTrashed()->updateOrCreate([
            'spotify_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$user->name}",
            'external_token' => $user->token,
            'external_refresh_token' => $user->refreshToken,
            'timezone' => get_timezone(),
            'ip_address' => request()->ip() ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
        ]);

        if ($user->wasRecentlyCreated) {
            $user->preferences()->create(['recieve_reminder_emails' => true]);
        }

        Log::channel('discord')->warning($user->name . ' just logged in!!');

        Auth::login($user);

        return redirect(route('home'));
    }

    public function logout()
    {
        Auth::logout();

        return redirect(route('welcome'))->with('success', "You've logged out. See ya next time!");
    }
}
