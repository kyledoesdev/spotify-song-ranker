<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource\RelationManagers;
use App\Jobs\SendEmailJob;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Artisan;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('subject'),
                RichEditor::make('content')
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Action::make('sendEmail')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->iconButton()
                    ->color('info')
                    ->modalHeading('Send Email Blast?')
                    ->modalDescription('Are you sure you\'d like to send the email blast?')
                    ->action(fn (EmailTemplate $record) => SendEmailJob::dispatch($record))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEmailTemplates::route('/'),
        ];
    }
}
