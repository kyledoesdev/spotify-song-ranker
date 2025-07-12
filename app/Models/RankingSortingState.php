<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankingSortingState extends Model
{
    protected $fillable = [
        'ranking_id',
        'sorting_state',
        'aprox_comparisons',
        'completed_comparisons',
    ];

    protected function casts(): array
    {
        return [
            'sorting_state' => 'array',
        ];
    }

    public function ranking(): BelongsTo
    {
        $this->belongsTo(Ranking::class);
    }
}
