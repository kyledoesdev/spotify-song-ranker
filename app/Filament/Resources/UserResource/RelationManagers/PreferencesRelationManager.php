<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PreferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'Preferences';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('recieve_reminder_emails')
                    ->boolean()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('recieve_reminder_emails')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\ToggleColumn::make('recieve_reminder_emails'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        return Carbon::parse($record->getAttributes()['created_at'])
                            ->inUserTimezone()
                            ->format('m/d/Y g:i A T');
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        return Carbon::parse($record->getAttributes()['updated_at'])
                            ->inUserTimezone()
                            ->format('m/d/Y g:i A T');
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
