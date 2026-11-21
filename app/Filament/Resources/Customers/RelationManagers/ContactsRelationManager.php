<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources/customer.tabs.contacts');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label(__('common.fields.position'))
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('common.fields.email'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('common.fields.phone'))
                    ->tel()
                    ->maxLength(50),
                Toggle::make('is_primary')
                    ->label(__('resources/customer.fields.primary_contact')),
                Textarea::make('notes')
                    ->label(__('common.fields.notes'))
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.fields.name'))
                    ->searchable(),
                TextColumn::make('position')
                    ->label(__('common.fields.position'))
                    ->placeholder(__('common.placeholders.none'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('common.fields.email'))
                    ->placeholder(__('common.placeholders.none'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('common.fields.phone'))
                    ->placeholder(__('common.placeholders.none')),
                TextColumn::make('is_primary')
                    ->label(__('common.fields.is_primary'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/customer.boolean.yes') : __('resources/customer.boolean.no')),
            ])
            ->defaultSort('is_primary', 'desc')
            ->emptyStateHeading(__('resources/customer.placeholders.empty_contacts'))
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                TableActions::group([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
