<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Str;

/** Caches the badge count, which otherwise runs per resource on every page load. */
trait HasCachedNavigationBadge
{
    public static function getNavigationBadge(): ?string
    {
        return short_number(cache()->remember(
            'filament:nav-badge:'.Str::replace('\\', '.', static::class),
            now()->addMinutes(5),
            fn (): int => static::navigationBadgeCount(),
        ));
    }

    protected static function navigationBadgeCount(): int
    {
        return static::getModel()::query()->count();
    }
}
