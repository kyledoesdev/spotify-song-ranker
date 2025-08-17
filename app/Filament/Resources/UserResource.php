<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\PreferencesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\RankingsRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('spotify_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('timezone')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timezone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rankings_count')
                    ->sortable()
                    ->label('Rankings')
                    ->counts('rankings'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Signed Up At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Logged In At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
                Section::make('User Information')
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->circular(),
                            ]),

                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id'),
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('timezone'),
                                TextEntry::make('ip_address')
                                    ->label('Ip Address'),
                                TextEntry::make('created_at')
                                    ->label('First Joined At')
                                    ->formatStateUsing(function ($state, $record) {
                                        return Carbon::parse($record->getAttributes()['created_at'])
                                            ->inUserTimezone()
                                            ->format('m/d/Y g:i A T');
                                    }),
                                TextEntry::make('updated_at')
                                    ->label('Last Logged In At')
                                    ->formatStateUsing(function ($state, $record) {
                                        return Carbon::parse($record->getAttributes()['updated_at'])
                                            ->inUserTimezone()
                                            ->format('m/d/Y g:i A T');
                                    }),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('', [
                RankingsRelationManager::class,
                PreferencesRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
