<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Support\Format;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('customer')
                    ->tabs([
                        Tab::make(__('resources/customer.tabs.detail'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Section::make(__('resources/customer.sections.detail'))
                                    ->schema([
                                        TextEntry::make('customer_code')->label(__('common.fields.code')),
                                        TextEntry::make('company_name')->label(__('common.fields.company_name')),
                                        TextEntry::make('customer_type')->label(__('common.fields.type'))->badge(),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('npwp')->label(__('common.fields.npwp'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('lead.company_name')->label(__('resources/customer.fields.origin_lead'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('payment_term_days')->label(__('resources/customer.fields.payment_term')),
                                        TextEntry::make('credit_limit')
                                            ->label(__('common.fields.credit_limit'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('outstanding_receivables')
                                            ->label(__('resources/customer.fields.outstanding_receivables'))
                                            ->state(fn ($record): string => Format::money($record->outstandingReceivables())),
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/customer.tabs.addresses'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('common.fields.address'))
                                    ->schema([
                                        RepeatableEntry::make('addresses')
                                            ->hiddenLabel()
                                            ->placeholder(__('resources/customer.placeholders.empty_addresses'))
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
                                                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/customer.boolean.yes') : __('resources/customer.boolean.no')),
                                                TextEntry::make('full_address')
                                                    ->label(__('resources/customer.fields.full_address'))
                                                    ->placeholder(__('common.placeholders.none'))
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),

                        Tab::make(__('resources/customer.tabs.contacts'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(__('resources/customer.sections.contact_person'))
                                    ->schema([
                                        RepeatableEntry::make('contacts')
                                            ->hiddenLabel()
                                            ->placeholder(__('resources/customer.placeholders.empty_contacts'))
                                            ->schema([
                                                TextEntry::make('name')->label(__('common.fields.name'))->weight('bold'),
                                                TextEntry::make('position')->label(__('common.fields.position'))->placeholder(__('common.placeholders.none')),
                                                TextEntry::make('email')->label(__('common.fields.email'))->placeholder(__('common.placeholders.none')),
                                                TextEntry::make('phone')->label(__('common.fields.phone'))->placeholder(__('common.placeholders.none')),
                                                TextEntry::make('is_primary')
                                                    ->label(__('common.fields.is_primary'))
                                                    ->badge()
                                                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/customer.boolean.yes') : __('resources/customer.boolean.no')),
                                            ])
                                            ->columns(5),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
