<?php

namespace App\Filament\Resources\Rankings\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\Rankings\RankingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRanking extends ViewRecord
{
    protected static string $resource = RankingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
