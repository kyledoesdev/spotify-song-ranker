<?php

namespace App\Models;

use App\Models\Model;

class Song extends Model {

    protected $fillable = [
        'ranking_id',
        'song_spotify_id',
        'title',
        'rank',
        'cover'
    ];

}
