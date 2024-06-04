<?php

namespace App\Models;

class Song extends Model
{
    protected $fillable = [
        'ranking_id',
        'song_spotify_id',
        'title',
        'rank',
        'cover',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($query) => $query->orderBy('rank', 'ASC'));
    }
}
