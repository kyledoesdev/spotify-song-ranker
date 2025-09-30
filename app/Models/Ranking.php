<?php

namespace App\Models;

use App\Enums\RankingType;
use App\QueryBuilders\RankingQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseEloquentBuilder(RankingQueryBuilder::class)]
class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'artist_id',
        'playlist_id',
        'name',
        'is_ranked',
        'is_public',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_ranked' => 'boolean',
            'has_podcast_episode' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): HasOne
    {
        return $this->hasOne(Artist::class, 'id', 'artist_id');
    }

    public function playlist(): HasOne
    {
        return $this->hasOne(Playlist::class, 'id', 'playlist_id');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function sortingState(): HasOne
    {
        return $this->hasOne(RankingSortingState::class, 'ranking_id', 'id');
    }

    public function getCompletedAtAttribute(): string
    {
        if ($this->attributes['completed_at'] == null) {
            return 'In Progress';
        }

        return Carbon::parse($this->attributes['completed_at'])->diffForHumans();
    }

    public function getFormattedCompletedAtAttribute(): string
    {
        if ($this->attributes['completed_at'] == null) {
            return 'In Progress';
        }

        return Carbon::parse($this->attributes['completed_at'])->inUserTimezone()->format('M d, Y g:i A T');
    }

    public function getTypeAttribute(): RankingType
    {
        return is_null($this->playlist_id) ? RankingType::ARTIST : RankingType::PLAYLIST;
    }

    public function canBeSeen(): bool
    {
        if ($this->user_id == auth()->id()) {
            return true;
        }

        return $this->is_public && $this->is_ranked;
    }
}
