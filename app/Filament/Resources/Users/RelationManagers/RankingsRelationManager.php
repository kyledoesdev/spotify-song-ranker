<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Rankings\RankingResource;
use App\Models\Ranking;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
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
                TextColumn::make('completed_at')
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
                    }),
                TextColumn::make('songs_count')
                    ->sortable()
                    ->label('Songs')
                    ->counts('songs'),
            ])
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
