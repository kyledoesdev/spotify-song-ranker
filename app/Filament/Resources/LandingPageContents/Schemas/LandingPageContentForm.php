<?php

namespace App\Filament\Resources\LandingPageContents\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LandingPageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Textarea::make('text')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->label('Content'),
            ]);
    }
}
