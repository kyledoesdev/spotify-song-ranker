<?php

namespace App\Livewire\Legal;

use App\Enums\LegalDocumentType;

class Terms extends LegalDocumentPage
{
    protected function type(): LegalDocumentType
    {
        return LegalDocumentType::TERMS;
    }
}
