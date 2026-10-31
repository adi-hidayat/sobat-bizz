<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Enums\SupplierStatus;
use App\Filament\Schemas\AddressRepeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SupplierForm
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
                                        TextInput::make('supplier_code')
                                            ->label(__('resources/supplier.fields.supplier_code'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        TextInput::make('company_name')
                                            ->label(__('common.fields.company_name'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('contact_person')
                                            ->label(__('resources/supplier.fields.contact_person'))
                                            ->maxLength(255),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(SupplierStatus::class)
                                            ->default(SupplierStatus::Active)
                                            ->required(),
                                        TextInput::make('email')
                                            ->label(__('common.fields.email'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('common.fields.phone'))
                                            ->tel()
                                            ->maxLength(50),
                                        TextInput::make('npwp')
                                            ->label(__('common.fields.npwp'))
                                            ->maxLength(50),
                                        TextInput::make('payment_term_days')
                                            ->label(__('common.fields.payment_term_days'))
                                            ->numeric()
                                            ->default(14)
                                            ->required(),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/supplier.tabs.addresses'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('common.fields.address'))
                                    ->schema([
                                        AddressRepeater::make(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
