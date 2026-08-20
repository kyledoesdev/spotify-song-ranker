<?php

namespace App\Models;

use App\Enums\LegalDocumentType;
use App\QueryBuilders\LegalDocumentQueryBuilder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;

#[UseEloquentBuilder(LegalDocumentQueryBuilder::class)]
class LegalDocument extends Model
{
    protected $fillable = [
        'type',
        'content',
        'effective_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => LegalDocumentType::class,
            'effective_at' => 'datetime',
        ];
    }
}
