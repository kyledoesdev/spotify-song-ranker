<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\RankingResource;
use App\Models\Ranking;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
    {
        return $form->schema(Ranking::getAdminForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(Ranking::getAdminTable($this->getOwnerRecord()->getKey()))
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => RankingResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => RankingResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
