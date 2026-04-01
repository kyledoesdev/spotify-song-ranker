<?php

namespace App\Filament\Resources\LandingPageContents\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LandingPageContentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text')
                    ->limit(60)
                    ->label('Content'),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->sortable()
                    ->dateTime(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
