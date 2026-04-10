<?php

namespace App\Enums;

enum RankingType: string
{
    case ARTIST = 'artist';
    case PLAYLIST = 'playlist';
    case SHOW = 'show';

    public function label(): string
    {
        return match ($this) {
            self::ARTIST => 'Artist',
            self::PLAYLIST => 'Playlist',
            self::SHOW => 'Podcast / Show',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ARTIST => 'fa-music',
            self::PLAYLIST => 'fa-bars-staggered',
            self::SHOW => 'fa-podcast',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ARTIST => 'purple',
            self::PLAYLIST => 'green',
            self::SHOW => 'blue',
        };
    }

    public function itemLabel(int $count = 2): string
    {
        return match ($this) {
            self::SHOW => $count === 1 ? 'episode' : 'episodes',
            default => $count === 1 ? 'track' : 'tracks',
        };
    }
}
