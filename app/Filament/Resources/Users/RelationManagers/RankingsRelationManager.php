<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Actions\Rankings\DestroyRanking;
use App\Filament\Resources\Rankings\RankingResource;
use App\Filament\Resources\Rankings\Tables\RankingTable;
use App\Models\Ranking;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RankingsRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return RankingResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return RankingTable::configure($table)
            ->recordTitleAttribute('name')
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ranking $record): string => RankingResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Ranking $record): string => RankingResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->using(fn (Ranking $record) => app(DestroyRanking::class)->handle($record->user, $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records->each(fn (Ranking $record) => app(DestroyRanking::class)->handle($record->user, $record));
                        }),
                ]),
            ]);
    }
}
