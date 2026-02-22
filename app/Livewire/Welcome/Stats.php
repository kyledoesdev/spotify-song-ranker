<?php

namespace App\Livewire\Welcome;

use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Component;

class Stats extends Component
{
    public static function getUserCount(): int
    {
        return cache()->remember('welcome:stats:users', now()->addDay(), fn () => round(User::count() / 50) * 50);
    }

    public static function getRankingCount(): int
    {
        return cache()->remember('welcome:stats:rankings', now()->addDay(), fn () => Ranking::publicRankedCount());
    }

    public static function getArtistCount(): int
    {
        return cache()->remember('welcome:stats:artists', now()->addDay(), fn () => Song::rankedArtistCount());
    }

    public function render()
    {
        return view('livewire.welcome.stats', [
            'users' => self::getUserCount(),
            'rankings' => self::getRankingCount(),
            'artists' => self::getArtistCount(),
        ]);
    }
}
