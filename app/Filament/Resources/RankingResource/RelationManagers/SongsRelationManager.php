<?php

namespace App\Filament\Resources\RankingResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SongsRelationManager extends RelationManager
{
    protected static string $relationship = 'Songs';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('cover'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextInputColumn::make('rank')
                    ->rules(['required', 'integer']),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
