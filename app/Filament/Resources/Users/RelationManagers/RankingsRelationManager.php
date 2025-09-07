<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Rankings\RankingResource;
use App\Models\Ranking;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RankingsRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(Ranking::getAdminForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(Ranking::getAdminTable($this->getOwnerRecord()->getKey()))
            ->filters([])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => RankingResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn ($record) => RankingResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
