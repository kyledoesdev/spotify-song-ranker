<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model
{
    protected $fillable = [
        'artist_id',
        'artist_name',
        'artist_img',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn (Builder $query) => $query->orderBy('artist_name', 'asc'));
    }

    public function ranking(): BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }

    #[Scope]
    public function topArtists(Builder $query)
    {
        $query->newQuery()
            ->selectRaw('
                count(rankings.artist_id) as artist_rankings_count,
                artists.id,
                artists.artist_name,
                artists.artist_img
            ')
            ->join('rankings', function ($join) {
                $join->on('rankings.artist_id', '=', 'artists.id')
                    ->whereNull('rankings.deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->groupBy('rankings.artist_id')
            ->orderBy('artist_rankings_count', 'desc')
            ->orderBy('artists.artist_name', 'asc');
    }
}
