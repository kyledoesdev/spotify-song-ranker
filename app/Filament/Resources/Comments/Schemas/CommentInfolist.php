<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Support\Icons\Heroicon;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->badge(),
                        TextEntry::make('parent_id')
                            ->label('Parent Comment')
                            ->placeholder('None (Top Level)'),
                        TextEntry::make('original_text')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('text')
                            ->columnSpanFull()
                            ->prose(),
                        KeyValueEntry::make('extra')
                            ->columnSpanFull()
                            ->placeholder('No extra data'),
                    ]),

                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('commentator.name')
                            ->label('User')
                            ->icon(Heroicon::Link)
                            ->url(fn ($record) => $record->commentator ? route('filament.admin.resources.users.edit', $record->commentator_id) : null),
                        TextEntry::make('commentable.name')
                            ->label('Ranking')
                            ->icon(Heroicon::Link)
                            ->url(fn ($record) => $record->commentable ? route('filament.admin.resources.rankings.edit', $record->commentator_id) : null),
                    ]),

                Section::make('Status & Timestamps')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('approved_at')
                            ->label('Approved')
                            ->dateTime()
                            ->placeholder('Not Approved')
                            ->icon(fn ($state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                            ->iconColor(fn ($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->since(),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->since(),
                    ]),
            ]);
    }
}
