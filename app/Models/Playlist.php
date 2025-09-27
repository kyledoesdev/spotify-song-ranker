<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Playlist extends Model
{
    protected $fillable = [
        'playlist_id',
        'creator_id',
        'creator_name',
        'name',
        'description',
        'cover',
        'track_count',
    ];

    public function ranking(): BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }
}
