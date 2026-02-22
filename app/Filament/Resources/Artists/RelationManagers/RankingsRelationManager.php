<?php

namespace App\Filament\Resources\Artists\RelationManagers;

use App\Filament\Resources\Rankings\RankingResource;
use App\Filament\Resources\Rankings\Tables\RankingTable;
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
            ->modifyQueryUsing(fn ($query) => $query->whereNull('playlist_id'))
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => RankingResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
