<?php

namespace App\Filament\Resources\ApplicationDashboards;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\ApplicationDashboards\Pages\ManageApplicationDashboards;
use App\Models\ApplicationDashboard;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ApplicationDashboardResource extends Resource
{
    protected static ?string $model = ApplicationDashboard::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'App Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('about_page')->columnSpanFull(),
                TextInput::make('version')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slideshow_speed')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('about_page')
                    ->limit(25),
                TextColumn::make('version')
                    ->searchable(),
                TextColumn::make('slideshow_speed')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageApplicationDashboards::route('/'),
        ];
    }
}
