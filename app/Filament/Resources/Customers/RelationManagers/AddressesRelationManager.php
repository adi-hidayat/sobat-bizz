<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Actions\TableActions;
use App\Filament\Schemas\AddressRepeater;
use App\Models\Address;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources/customer.tabs.addresses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(AddressRepeater::fields())
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('type')
                    ->label(__('common.fields.address_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Address::TYPE_BILLING => __('common.address_types.billing'),
                        Address::TYPE_SHIPPING => __('common.address_types.shipping'),
                        Address::TYPE_OFFICE => __('common.address_types.office'),
                        default => __('common.address_types.other'),
                    }),
                TextColumn::make('label')
                    ->label(__('common.fields.label'))
                    ->placeholder(__('common.placeholders.none'))
                    ->searchable(),
                TextColumn::make('contact_name')
                    ->label(__('common.fields.contact_name'))
                    ->placeholder(__('common.placeholders.none'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('common.fields.phone'))
                    ->placeholder(__('common.placeholders.none')),
                TextColumn::make('full_address')
                    ->label(__('resources/customer.fields.full_address'))
                    ->placeholder(__('common.placeholders.none'))
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('is_primary')
                    ->label(__('common.fields.is_primary'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/customer.boolean.yes') : __('resources/customer.boolean.no')),
            ])
            ->defaultSort('is_primary', 'desc')
            ->emptyStateHeading(__('resources/customer.placeholders.empty_addresses'))
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
