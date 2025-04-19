<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RankingResource\Pages;
use App\Filament\Resources\RankingResource\RelationManagers;
use App\Filament\Resources\RankingResource\RelationManagers\SongsRelationManager;
use App\Models\Ranking;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RankingResource extends Resource
{
    protected static ?string $model = Ranking::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form->schema(Ranking::getAdminForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Ranking::getAdminTable())
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Ranking Information')
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Creator')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('name')
                                    ->url(fn (Ranking $ranking) => route('rank.show', ['id' => $ranking->getKey()]))
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
                                    ->label('Completed At')
                                    ->formatStateUsing(function ($state, $record) {
                                        $timestamp = $record->getAttributes()['completed_at'];

                                        if (is_null($timestamp)) {
                                            return 'In Progress';
                                        }

                                        return Carbon::parse($timestamp)
                                            ->inUserTimezone()
                                            ->format('m/d/Y g:i A T');
                                    }),
                            ])
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SongsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRankings::route('/'),
            'view' => Pages\ViewRanking::route('/{record}'),
            'edit' => Pages\EditRanking::route('/{record}/edit'),
        ];
    }
}
