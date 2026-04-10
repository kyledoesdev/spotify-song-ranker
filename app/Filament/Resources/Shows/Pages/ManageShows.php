<?php

namespace App\Filament\Resources\Shows\Pages;

use App\Filament\Resources\Shows\ShowResource;
use Filament\Resources\Pages\ManageRecords;

class ManageShows extends ManageRecords
{
    protected static string $resource = ShowResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
