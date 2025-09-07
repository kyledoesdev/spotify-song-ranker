<?php

namespace App\Filament\Resources\Artists\Pages;

use App\Filament\Resources\Artists\ArtistResource;
use Filament\Resources\Pages\ListRecords;

class ListArtists extends ListRecords
{
    protected static string $resource = ArtistResource::class;
}
