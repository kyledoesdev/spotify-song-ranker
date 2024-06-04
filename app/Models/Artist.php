<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model
{
    protected $fillable = [
        'artist_id',
        'artist_name',
        'artist_img',
    ];

    public function ranking(): BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }
}
