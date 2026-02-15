<?php

namespace App\Filament\Resources\Terms;

use App\Filament\Resources\Terms\Pages\CreateTerms;
use App\Filament\Resources\Terms\Pages\EditTerms;
use App\Filament\Resources\Terms\Pages\ListTerms;
use App\Filament\Resources\Terms\Pages\ViewTerms;
use App\Filament\Resources\Terms\Schemas\TermsForm;
use App\Filament\Resources\Terms\Schemas\TermsInfolist;
use App\Filament\Resources\Terms\Tables\TermsTable;
use App\Models\Terms;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TermsResource extends Resource
{
    protected static ?string $model = Terms::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return TermsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TermsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTerms::route('/'),
            'create' => CreateTerms::route('/create'),
            'view' => ViewTerms::route('/{record}'),
            'edit' => EditTerms::route('/{record}/edit'),
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
        return short_number(static::getModel()::count());
    }
}
