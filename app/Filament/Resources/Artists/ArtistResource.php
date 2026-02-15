<?php

namespace App\Filament\Resources\Artists;

use App\Filament\Resources\Artists\Pages\ListArtists;
use App\Models\Artist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ArtistResource extends Resource
{
    protected static ?string $model = Artist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static string|UnitEnum|null $navigationGroup = 'Song Rank';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('artist_img')
                    ->label('')
                    ->circular(),
                TextColumn::make('artist_id')
                    ->label('Artist ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('artist_name')
                    ->label('Artist Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtists::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return short_number(static::getModel()::count());
    }
}
