<?php

namespace App\Filament\Resources\Terms\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class TermsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('content')->columnSpanFull()
            ]);
    }
}
