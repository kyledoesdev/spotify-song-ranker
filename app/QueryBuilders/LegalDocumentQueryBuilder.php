<?php

namespace App\QueryBuilders;

use App\Enums\LegalDocumentType;
use App\Models\LegalDocument;
use Illuminate\Database\Eloquent\Builder;

class LegalDocumentQueryBuilder extends Builder
{
    /**
     * The document currently in force for the given type, or null when nothing is published yet.
     */
    public function currentFor(LegalDocumentType $type): ?LegalDocument
    {
        return $this->newQuery()
            ->ofType($type)
            ->published()
            ->latest('effective_at')
            ->first();
    }

    public function ofType(LegalDocumentType $type): static
    {
        return $this->where('type', $type);
    }

    public function published(): static
    {
        return $this->where('effective_at', '<=', now());
    }
}
