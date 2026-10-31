<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Filament\Schemas\AddressRepeater;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CustomerForm
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
                                        TextInput::make('customer_code')
                                            ->label(__('resources/customer.fields.customer_code'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        TextInput::make('company_name')
                                            ->label(__('common.fields.company_name'))
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('customer_type')
                                            ->label(__('resources/customer.fields.customer_type'))
                                            ->options(CustomerType::class)
                                            ->default(CustomerType::Company)
                                            ->required(),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(CustomerStatus::class)
                                            ->default(CustomerStatus::Active)
                                            ->required(),
                                        TextInput::make('npwp')
                                            ->label(__('common.fields.npwp'))
                                            ->maxLength(50),
                                        Select::make('lead_id')
                                            ->label(__('resources/customer.fields.origin_lead'))
                                            ->relationship('lead', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('payment_term_days')
                                            ->label(__('common.fields.payment_term_days'))
                                            ->numeric()
                                            ->default(30)
                                            ->required(),
                                        TextInput::make('credit_limit')
                                            ->label(__('common.fields.credit_limit'))
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp'),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/customer.tabs.addresses'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('common.fields.address'))
                                    ->schema([
                                        AddressRepeater::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/customer.tabs.contacts'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(__('resources/customer.sections.contact_person'))
                                    ->schema([
                                        Repeater::make('contacts')
                                            ->relationship()
                                            ->hiddenLabel()
                                            ->schema([
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
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel(__('common.actions.add_contact'))
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
