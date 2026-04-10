<?php

namespace App\Models;

use App\Contracts\Rankable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model implements Rankable
{
    protected $fillable = [
        'show_id',
        'publisher',
        'name',
        'description',
        'cover',
        'episode_count',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'description' => 'string',
            'data' => 'array',
        ];
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }

    public function getDescriptionAttribute($value): ?string
    {
        return html_entity_decode($value);
    }

    public function cover(): ?string
    {
        return $this->attributes['cover'] ?? null;
    }

    public function name(): string
    {
        return $this->attributes['name'];
    }

    public function spotifyId(): string
    {
        return $this->show_id;
    }

    public function spotifyUrl(): string
    {
        return "https://open.spotify.com/show/{$this->show_id}";
    }
}
