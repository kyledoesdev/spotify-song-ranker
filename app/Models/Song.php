<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function ranking(): BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }

    public function artist(): HasOne
    {
        return $this->hasOne(Artist::class, 'id', 'artist_id');
    }
}
