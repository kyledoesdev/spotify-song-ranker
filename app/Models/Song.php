<?php

namespace App\Models;

use App\Models\Model;

class Song extends Model
{
    protected $fillable = [
        'ranking_id',
        'spotify_song_id',
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
