<?php

namespace App\Filament\Resources\RankingResource\Pages;

use App\Filament\Resources\RankingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRanking extends ViewRecord
{
    protected static string $resource = RankingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
