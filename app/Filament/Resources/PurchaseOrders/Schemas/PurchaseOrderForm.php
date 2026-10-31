<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Enums\PurchaseOrderStatus;
use App\Filament\Schemas\DocumentTotalsFields;
use App\Filament\Schemas\RenderItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('purchase_order')
                    ->tabs([
                        Tab::make(__('resources/purchase_order.tabs.information'))
                            ->icon('heroicon-o-shopping-cart')
                            ->schema([
                                Section::make(__('resources/purchase_order.sections.information'))
                                    ->schema([
                                        TextInput::make('po_number')
                                            ->label(__('resources/purchase_order.fields.po_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('supplier_id')
                                            ->label(__('common.fields.supplier'))
                                            ->relationship('supplier', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('po_date')
                                            ->label(__('common.fields.po_date'))
                                            ->default(now())
                                            ->required(),
                                        DatePicker::make('expected_delivery_date')
                                            ->label(__('documents.fields.expected_arrival')),
                                        TextInput::make('currency')
                                            ->label(__('common.fields.currency'))
                                            ->default('IDR')
                                            ->required()
                                            ->maxLength(3),
                                        TextInput::make('payment_terms')
                                            ->label(__('documents.fields.payment_terms'))
                                            ->maxLength(255),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(PurchaseOrderStatus::class)
                                            ->default(PurchaseOrderStatus::Draft)
                                            ->required(),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/purchase_order.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/purchase_order.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/purchase_order.tabs.summary'))
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                DocumentTotalsFields::section(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
