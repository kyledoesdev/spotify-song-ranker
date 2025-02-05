<?php

namespace App\Models;

use App\Models\Model;
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

    public static function getTopArtists()
    {
        return self::query()
            ->selectRaw('
                count(rankings.artist_id) as artist_rankings_count,
                artists.id,
                artists.artist_name
            ')
            ->join('rankings', function($join) {
                $join->on('rankings.artist_id', '=', 'artists.id')
                    ->whereNull('deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->groupBy('rankings.artist_id')
            ->orderBy('artist_rankings_count', 'desc')
            ->orderBy('artists.artist_name', 'asc')
            ->limit(10)
            ->get();
    }
}
