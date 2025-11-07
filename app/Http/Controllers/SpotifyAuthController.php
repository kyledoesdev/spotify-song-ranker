<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Stats\LoginStat;
use App\Stats\LogoutStat;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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
        try {
            $user = Socialite::driver('spotify')->user();
        } catch (InvalidStateException|ClientException) {
            return redirect(route('welcome'))->withErrors(['error' => 'There was an issue with your spotify authorization token. Please try logging in again.']);
        }

        $deletedUser = User::query()
            ->withTrashed()
            ->where('spotify_id', $user->id)
            ->whereNotNull('deleted_at')
            ->first();

        if (! is_null($deletedUser)) {
            Log::channel('discord_user_updates')->warning($user->name.' is back from the dead!!!!');
            $deletedUser->restore();
            session()->flash('success', "Welcome back {$user->name}.. we've been expecting you.. To revive your rankings - reach out via the support bubble in the bottom right.");
        }

        $user = User::withTrashed()->updateOrCreate([
            'spotify_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$user->name}",
            'external_token' => $user->token,
            'external_refresh_token' => $user->refreshToken,
            'timezone' => timezone(),
            'ip_address' => request()->ip() ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
            'user_packet' => zuck(),
        ]);

        if ($user->wasRecentlyCreated) {
            $user->preferences()->create();
            Log::channel('discord_user_updates')->warning("New User: $user->name ($user->email) just logged in!!");
        } else {
            Log::channel('discord_user_updates')->warning("$user->name ($user->email) just logged in!!");
        }

        Session::regenerate();

        Auth::login($user);
        
        defer(fn() => LoginStat::increase());

        return redirect(route('dashboard'));
    }

    public function logout()
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        defer(fn() => LogoutStat::increase());

        return redirect(route('welcome'))->with('success', "You've logged out. See ya next time!");
    }
}
