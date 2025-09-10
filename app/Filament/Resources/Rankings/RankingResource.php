<?php

namespace App\Filament\Resources\Rankings;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Filament\Resources\Rankings\Pages\ListRankings;
use App\Filament\Resources\Rankings\Pages\ViewRanking;
use App\Filament\Resources\Rankings\Pages\EditRanking;
use App\Filament\Resources\RankingResource\Pages;
use App\Filament\Resources\Rankings\RelationManagers\SongsRelationManager;
use App\Models\Ranking;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RankingResource extends Resource
{
    protected static ?string $model = Ranking::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(Ranking::getAdminForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Ranking::getAdminTable())
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function infoList(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn (Ranking $ranking) => "Ranking ID: {$ranking->getKey()}.")
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Creator')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('name')
                                    ->url(fn (Ranking $ranking) => route('ranking', ['id' => $ranking->getKey()]))
                                    ->icon('heroicon-m-link'),
                                TextEntry::make('artist.artist_name')
                                    ->icon('heroicon-m-musical-note'),
                            ]),
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->columns(2)
                                    ->schema([
                                        IconEntry::make('is_ranked')
                                            ->label('Is Ranked')
                                            ->boolean(),
                                        IconEntry::make('is_public')
                                            ->label('Is Public')
                                            ->boolean(),
                                    ]),
                                TextEntry::make('completed_at')
                                    ->label('Completed At'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SongsRelationManager::class,
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
        return Ranking::query()
            ->where('is_ranked', true)
            ->where('is_public', true)
            ->count();
    }
}
