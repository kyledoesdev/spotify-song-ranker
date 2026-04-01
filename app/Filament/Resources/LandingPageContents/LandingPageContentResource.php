<?php

namespace App\Filament\Resources\LandingPageContents;

use App\Filament\Resources\LandingPageContents\Pages\EditLandingPageContent;
use App\Filament\Resources\LandingPageContents\Pages\ListLandingPageContents;
use App\Filament\Resources\LandingPageContents\Schemas\LandingPageContentForm;
use App\Filament\Resources\LandingPageContents\Tables\LandingPageContentTable;
use App\Models\LandingPageContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LandingPageContentResource extends Resource
{
    protected static ?string $model = LandingPageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Landing Page';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return LandingPageContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingPageContentTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingPageContents::route('/'),
            'edit' => EditLandingPageContent::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return short_number(LandingPageContent::count());
    }
}
