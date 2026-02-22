<?php

namespace App\Filament\Resources\Rankings\Tables;

use Carbon\Carbon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RankingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::columns())
            ->filters([]);
    }

    /**
     * @return array<int, TextColumn|IconColumn>
     */
    public static function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('ID')
                ->searchable()
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Creator')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            TextColumn::make('artist.artist_name')
                ->label('Artist')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('playlist.name')
                ->label('Playlist')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            IconColumn::make('is_ranked')
                ->label('Is Ranked')
                ->boolean()
                ->searchable()
                ->sortable()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark')
                ->toggleable(isToggledHiddenByDefault: false),
            IconColumn::make('is_public')
                ->label('Is Public')
                ->boolean()
                ->searchable()
                ->sortable()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('created_at')
                ->label('Created')
                ->searchable()
                ->sortable()
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: false),
            static::completedAtColumn(),
            TextColumn::make('songs_count')
                ->sortable()
                ->label('Songs')
                ->counts('songs'),
        ];
    }

    public static function completedAtColumn(): TextColumn
    {
        return TextColumn::make('completed_at')
            ->label('Completed')
            ->searchable()
            ->sortable()
            ->dateTime()
            ->toggleable(isToggledHiddenByDefault: false)
            ->formatStateUsing(function ($state, $record) {
                $timestamp = $record->getAttributes()['completed_at'];

                if (is_null($timestamp)) {
                    return 'In Progress';
                }

                return Carbon::parse($timestamp)
                    ->inUserTimezone()
                    ->format('m/d/Y g:i A T');
            });
    }
}
