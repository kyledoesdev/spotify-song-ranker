<?php

namespace App\Livewire\Legal;

use App\Enums\LegalDocumentType;

class Privacy extends LegalDocumentPage
{
    protected function type(): LegalDocumentType
    {
        return LegalDocumentType::PRIVACY;
    }
}
