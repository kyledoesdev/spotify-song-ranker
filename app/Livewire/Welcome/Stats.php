<?php

namespace App\Livewire\Welcome;

use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Component;

class Stats extends Component
{
    public static function getUserCount(): int
    {
        return cache()->remember('welcome:stats:users', now()->addDay(), fn () => User::query()->roundedUserCount());
    }

    public static function getRankingCount(): int
    {
        return cache()->remember('welcome:stats:rankings', now()->addDay(), fn () => Ranking::query()->publicRankedCount());
    }

    public static function getArtistCount(): int
    {
        return cache()->remember('welcome:stats:artists', now()->addDay(), fn () => Song::query()->rankedArtistCount());
    }

    public static function getPlaylistCount(): int
    {
        return cache()->remember('welcome:stats:playlists', now()->addDay(), fn () => Playlist::query()->rankedPlaylistCount());
    }

    public function render()
    {
        return view('livewire.welcome.stats', [
            'users' => self::getUserCount(),
            'rankings' => self::getRankingCount(),
            'artists' => self::getArtistCount(),
            'playlists' => self::getPlaylistCount(),
        ]);
    }
}
