<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PreferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'Preferences';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('recieve_reminder_emails')
                    ->boolean()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('recieve_reminder_emails')
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                ToggleColumn::make('recieve_reminder_emails'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        return Carbon::parse($record->getAttributes()['created_at'])
                            ->inUserTimezone()
                            ->format('m/d/Y g:i A T');
                    }),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state, $record) {
                        return Carbon::parse($record->getAttributes()['updated_at'])
                            ->inUserTimezone()
                            ->format('m/d/Y g:i A T');
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
