<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateUsernameInComments;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Kyledoesdev\Essentials\Stats\LoginStat;
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
            $spotifyUser = Socialite::driver('spotify')->user();
        } catch (InvalidStateException|ClientException) {
            return redirect(route('welcome'))->withErrors(['error' => 'There was an issue with your spotify authorization token. Please try logging in again.']);
        }

        $deletedUser = User::query()
            ->withTrashed()
            ->where('spotify_id', $spotifyUser->id)
            ->whereNotNull('deleted_at')
            ->first();

        if (! is_null($deletedUser)) {
            Log::channel('discord_user_updates')->warning($spotifyUser->name.' is back from the dead!!!!');
            $deletedUser->restore();
            session()->flash('success', "Welcome back {$spotifyUser->name}.. we've been expecting you.. To revive your rankings - reach out via the support bubble in the bottom right.");
        }

        // disabled because we aren't handling mentions currently.
        /* if ($spotifyUser->name != $currentUser = User::firstWhere('spotify_id', $spotifyUser->id)?->name) {
            UpdateUsernameInComments::dispatch($currentUser);
        } */

        $songrankUser = User::withTrashed()->updateOrCreate([
            'spotify_id' => $spotifyUser->id,
        ], [
            'name' => $spotifyUser->name,
            'avatar' => $spotifyUser->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$spotifyUser->name}",
            'external_token' => $spotifyUser->token,
            'external_refresh_token' => $spotifyUser->refreshToken,
            'timezone' => $this->getUserTimezone(),
            'ip_address' => request()->ip() ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
            'user_packet' => zuck(),
        ]);

        $songrankUser->email ??= $spotifyUser->email ?? "{$spotifyUser->id}@songrank.dev";
        $songrankUser->save();

        if ($songrankUser->wasRecentlyCreated) {
            $songrankUser->preferences()->create();
        }

        Session::regenerate();

        Auth::login($songrankUser);
        
        LoginStat::increase();

        return redirect(route('dashboard'));
    }

    public function logout()
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect(route('welcome'))->with('success', "You've logged out. See ya next time!");
    }

    /* TODO - this is shit */
    private function getUserTimezone()
    {
        $tz = timezone();

        if ($tz == 'Europe/Kiev') {
            return 'Europe/Kyiv';
        }

        return $tz;
    }
}
