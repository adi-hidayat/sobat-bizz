<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SupplierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('supplier')
                    ->tabs([
                        Tab::make(__('resources/supplier.tabs.detail'))
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make(__('resources/supplier.sections.information'))
                                    ->schema([
                                        TextEntry::make('supplier_code')->label(__('common.fields.code')),
                                        TextEntry::make('company_name')->label(__('common.fields.company_name')),
                                        TextEntry::make('contact_person')->label(__('resources/supplier.fields.contact_person'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('email')->label(__('common.fields.email'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('phone')->label(__('common.fields.phone'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('npwp')->label(__('common.fields.npwp'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('payment_term_days')->label(__('resources/supplier.fields.payment_term')),
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/supplier.tabs.addresses'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('common.fields.address'))
                                    ->schema([
                                        RepeatableEntry::make('addresses')
                                            ->hiddenLabel()
                                            ->placeholder(__('resources/supplier.placeholders.empty_addresses'))
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->label(__('common.fields.type'))
                                                    ->badge()
                                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                                        'billing', 'shipping', 'office' => __('common.address_types.'.$state),
                                                        default => __('common.address_types.other'),
                                                    }),
                                                TextEntry::make('label')->label(__('common.fields.label'))->placeholder(__('common.placeholders.none')),
                                                TextEntry::make('is_primary')
                                                    ->label(__('common.fields.is_primary'))
                                                    ->badge()
                                                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/supplier.boolean.yes') : __('resources/supplier.boolean.no')),
                                                TextEntry::make('full_address')
                                                    ->label(__('resources/supplier.fields.full_address'))
                                                    ->placeholder(__('common.placeholders.none'))
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
