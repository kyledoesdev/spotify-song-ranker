<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\Stats\Models\StatsEvent;
use Spatie\Stats\StatsQuery;
use Spatie\Stats\StatsWriter;

trait StatsAfterEvents
{
    public static function bootStatsAfterEvents()
    {
        static::created(function (Model $model) {
            dispatch(fn() => StatsWriter::for(StatsEvent::class, ['name' => $model::statsClass()])->increase());
        });

        static::deleted(function (Model $model) {
            dispatch(fn() => StatsWriter::for(StatsEvent::class, ['name' => $model::statsClass()])->decrease());
        });
    }

    public static function statsQuery(): StatsQuery
    {
        return StatsQuery::for(StatsEvent::class, [
            'name' => static::statsClass(),
        ]);
    }

    public static function statsClass(): string
    {
        return static::class;
    }
}