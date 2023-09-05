<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model {

    protected $fillable = [
        'artist_id',
        'artist_name',
        'artist_img',
    ];

    public static function boot() {
        parent::boot();

        //call for and update artist img.
    }

    public function ranking() : BelongsTo {
        return $this->belongsTo(Ranking::class);
    }

}
