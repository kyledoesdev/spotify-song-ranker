<?php

namespace App\Filament\Resources\LegalDocuments\Schemas;

use App\Enums\LegalDocumentType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LegalDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(collect(LegalDocumentType::cases())
                        ->mapWithKeys(fn (LegalDocumentType $type) => [$type->value => $type->label()])
                    )
                    ->required(),
                DateTimePicker::make('effective_at')
                    ->label('Effective From')
                    ->helperText('The document with the most recent effective date, in the past, is the one shown on the site.')
                    ->default(now())
                    ->required(),
                RichEditor::make('content')->columnSpanFull(),
            ]);
    }
}
