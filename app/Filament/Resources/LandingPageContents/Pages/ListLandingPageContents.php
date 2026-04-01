<?php

namespace App\Filament\Resources\LandingPageContents\Pages;

use App\Filament\Resources\LandingPageContents\LandingPageContentResource;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageContents extends ListRecords
{
    protected static string $resource = LandingPageContentResource::class;
}
