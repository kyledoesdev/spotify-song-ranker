<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Song extends Model
{
    protected $fillable = [
        'ranking_id',
        'spotify_song_id',
        'uuid',
        'title',
        'rank',
        'cover',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn (Builder $query) => $query->orderBy('rank', 'asc'));
    }
}
