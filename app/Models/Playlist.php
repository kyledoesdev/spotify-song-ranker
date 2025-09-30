<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function casts(): array
    {
        return [
            'description' => 'string',
        ];
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'spotify_id')->withTrashed();
    }

    public function getDescriptionAttribute($value): ?string
    {
        return html_entity_decode($value);
    }

    #[Scope]
    public function topPlaylists(Builder $query)
    {
        $query->newQuery()
            ->selectRaw('
                count(rankings.playlist_id) as playlist_rankings_count,
                playlists.id,
                playlists.playlist_id,
                playlists.name,
                playlists.cover,
                playlists.creator_name
            ')
            ->join('rankings', function ($join) {
                $join->on('rankings.playlist_id', '=', 'playlists.id')
                    ->whereNull('rankings.deleted_at')
                    ->where('rankings.is_ranked', true)
                    ->where('rankings.is_public', true);
            })
            ->groupBy('rankings.playlist_id')
            ->orderBy('playlist_rankings_count', 'desc')
            ->orderBy('playlists.name', 'asc');
    }
}
