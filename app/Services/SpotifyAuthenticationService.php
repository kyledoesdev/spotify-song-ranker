<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use SocialiteProviders\Manager\OAuth2\User as SpotifyUser;

class SpotifyAuthenticationService
{
    public function __construct(
        private SpotifyUser $spotifyUser,
    ) {}

    /**
     * Returns the SongRank user for the Spotify OAuth User returned via Socialite.
     */
    public function getSongRankUser(): User
    {
        $songrankUser = User::withTrashed()->updateOrCreate([
            'spotify_id' => $this->spotifyUser->id,
        ], [
            'name' => $this->spotifyUser->name,
            'avatar' => $this->getAvatar(),
            'external_token' => $this->spotifyUser->token,
            'external_refresh_token' => $this->spotifyUser->refreshToken,
            'timezone' => timezone(),
            'ip_address' => request()->ip() ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
            'user_packet' => $this->getLocation(),
        ]);

        if (is_null($songrankUser->email)) {
            $this->resolveEmail($songrankUser);
        }

        if ($songrankUser->wasRecentlyCreated) {
            $songrankUser->preferences()->create();
        }

        return $songrankUser;
    }

    /**
     * Determines if a Spotify OAuth User has a `deleted` Song Rank user record.
     * If so, restore the user and return true - otherwise return false.
     */
    public function restoreUserIfAccountWasPreviouslyDeleted(): bool
    {
        $deletedUser = User::query()
            ->wherePreviouslyDeleted($this->spotifyUser->id)
            ->first();

        if (is_null($deletedUser)) {
            return false;
        }

        Log::channel('discord_user_updates')->warning($this->spotifyUser->name.' is back from the dead!!!!');

        $deletedUser->restore();

        return true;
    }

    /**
     * Some Spotify OAuth users do not have an email address associated with their account.
     * Resolve the Song Rank user's email by checking that all Song Rank users, including trashed,
     * do not share the current Spotify User's email.
     */
    private function resolveEmail(User $songrankUser): void
    {
        $email = $this->getEmail();

        $exists = User::query()
            ->whereEmailBelongsToAnotherUser($email, $songrankUser->getKey())
            ->exists();

        if ($exists) {
            Log::channel('discord_user_updates')->warning("Could not resolve email: {$email} for user {$songrankUser->getKey()} - the address is already taken. Check for duplicate accounts on spotify id {$songrankUser->spotify_id}.");

            return;
        }

        $songrankUser->email = $email;
        $songrankUser->save();
    }

    private function getLocation(): array
    {
        return collect(zuck())
            ->only(['country', 'countryCode', 'regionName', 'city'])
            ->all();
    }

    /**
     * Get the Spotify User's avatar, falling back to a generated one.
     */
    private function getAvatar(): string
    {
        return $this->spotifyUser->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$this->spotifyUser->name}";
    }

    /**
     * Get the Spotify User's email address, falling back to one derived from their spotify id.
     */
    private function getEmail(): string
    {
        return $this->spotifyUser->email ?? "{$this->spotifyUser->id}@songrank.dev";
    }
}
