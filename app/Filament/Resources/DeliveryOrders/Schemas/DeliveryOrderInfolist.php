<?php

namespace App\Filament\Resources\DeliveryOrders\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DeliveryOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('delivery_order')
                    ->tabs([
                        Tab::make(__('resources/delivery_order.tabs.summary'))
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make(__('resources/delivery_order.sections.information'))
                                    ->schema([
                                        TextEntry::make('do_number')->label(__('common.fields.number')),
                                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                                        TextEntry::make('delivery_date')->label(__('common.fields.date'))->date('d M Y'),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('delivery_method')->label(__('common.fields.method'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('received_by')->label(__('common.fields.received_by'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('shipping_address')->label(__('resources/delivery_order.fields.shipping_address_short'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('delivery_notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/delivery_order.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/delivery_order.sections.items')),
                            ]),

                        Tab::make(__('resources/delivery_order.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/delivery_order.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/delivery_order.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                        TextEntry::make('target_documents')
                                            ->label(__('resources/delivery_order.fields.target_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::targets($record)),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
