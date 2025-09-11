<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\RelationManagers\PreferencesRelationManager;
use App\Filament\Resources\Users\RelationManagers\RankingsRelationManager;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('spotify_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('timezone')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rankings_count')
                    ->sortable()
                    ->label('Rankings')
                    ->counts('rankings'),
                TextColumn::make('created_at')
                    ->label('Signed Up At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label('Last Logged In At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
                Section::make('User Information')
                    ->columns(3)
                    ->columnSpanFull()
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

                        TextEntry::make('user_packet')
                            ->label('User Packet')
                            ->markdown()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'No data';
                                                                
                                return collect($state)
                                    ->map(fn($value, $key) => "- **{$key}:** {$value}")
                                    ->join("\n");
                            })
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
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
