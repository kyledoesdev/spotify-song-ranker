<?php

namespace App\Filament\Resources\Terms\Pages;

use App\Filament\Resources\Terms\TermsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTerms extends ViewRecord
{
    protected static string $resource = TermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
