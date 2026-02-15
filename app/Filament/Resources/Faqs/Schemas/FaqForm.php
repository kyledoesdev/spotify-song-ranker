<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->required()
                    ->maxLength(255),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                RichEditor::make('text')
                    ->required()
                    ->columnSpanFull()
                    ->label('Answer'),
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }
}
