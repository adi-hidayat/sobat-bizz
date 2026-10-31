<?php

namespace App\Filament\Resources\DeliveryOrders\Schemas;

use App\Enums\DeliveryOrderStatus;
use App\Filament\Schemas\RenderItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DeliveryOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('delivery_order')
                    ->tabs([
                        Tab::make(__('resources/delivery_order.tabs.information'))
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make(__('resources/delivery_order.sections.information'))
                                    ->schema([
                                        TextInput::make('do_number')
                                            ->label(__('resources/delivery_order.fields.do_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('customer_id')
                                            ->label(__('common.fields.customer'))
                                            ->relationship('customer', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('delivery_date')
                                            ->label(__('common.fields.delivery_date'))
                                            ->default(now())
                                            ->required(),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(DeliveryOrderStatus::class)
                                            ->default(DeliveryOrderStatus::Draft)
                                            ->required(),
                                        TextInput::make('delivery_method')
                                            ->label(__('common.fields.delivery_method'))
                                            ->maxLength(255),
                                        TextInput::make('received_by')
                                            ->label(__('common.fields.received_by'))
                                            ->maxLength(255),
                                        Textarea::make('shipping_address')
                                            ->label(__('resources/delivery_order.fields.shipping_address'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('delivery_notes')
                                            ->label(__('common.fields.delivery_notes'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/delivery_order.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/delivery_order.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
