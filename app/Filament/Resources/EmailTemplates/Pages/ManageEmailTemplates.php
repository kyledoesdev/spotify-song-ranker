<?php

namespace App\Filament\Resources\EmailTemplates\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\EmailTemplates\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEmailTemplates extends ManageRecords
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add New Template'),
        ];
    }
}
