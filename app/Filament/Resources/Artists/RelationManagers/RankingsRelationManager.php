<?php

namespace App\Filament\Resources\Artists\RelationManagers;

use App\Filament\Resources\Rankings\RankingResource;
use App\Filament\Resources\Rankings\Tables\RankingTable;
use App\Models\Ranking;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class RankingsRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';

    public function table(Table $table): Table
    {
        return RankingTable::configure($table)
            ->recordTitleAttribute('name')
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ranking $record): string => RankingResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
