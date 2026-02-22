<?php

namespace App\Filament\Resources\Rankings;

use App\Filament\Resources\Rankings\Pages\EditRanking;
use App\Filament\Resources\Rankings\Pages\ListRankings;
use App\Filament\Resources\Rankings\Pages\ViewRanking;
use App\Filament\Resources\Rankings\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Rankings\RelationManagers\SongsRelationManager;
use App\Filament\Resources\Rankings\Tables\RankingTable;
use App\Models\Ranking;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RankingResource extends Resource
{
    protected static ?string $model = Ranking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Song Rank';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            DatePicker::make('completed_at')
                ->label('Completed At'),
            Toggle::make('is_ranked')
                ->label('Is Ranked')
                ->required(),
            Toggle::make('is_public')
                ->label('Is Public')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return RankingTable::configure($table)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function infoList(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ranking Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Creator')
                            ->icon(Heroicon::User),
                        TextEntry::make('name')
                            ->url(fn (Ranking $ranking) => route('ranking', ['id' => $ranking->getKey()]))
                            ->icon(Heroicon::Link),
                        TextEntry::make('artist.artist_name')
                            ->icon(Heroicon::MusicalNote),
                        TextEntry::make('completed_at')
                            ->icon(Heroicon::Clock)
                            ->label('Completed At'),
                        IconEntry::make('is_ranked')
                            ->label('Is Ranked')
                            ->boolean(),
                        IconEntry::make('is_public')
                            ->label('Is Public')
                            ->boolean(),
                    ]),

                Section::make('Playlist Details')
                    ->schema([
                        IconEntry::make('playlist_id')
                            ->label('Has Playlist?')
                            ->state(fn (Ranking $ranking) => ! is_null($ranking->playlist_id))
                            ->boolean(),
                        TextEntry::make('playlist.name')
                            ->label('Playlist Name'),
                        TextEntry::make('playlist.description')
                            ->label('Playlist Description'),
                        TextEntry::make('playlist.track_count')
                            ->label('Tracks in Playlist'),
                        TextEntry::make('songs_count')
                            ->label('Tracks Ranked')
                            ->state(fn (Ranking $ranking) => $ranking->songs()->count()),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SongsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRankings::route('/'),
            'view' => ViewRanking::route('/{record}'),
            'edit' => EditRanking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return short_number(
            Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->count()
        );
    }
}
