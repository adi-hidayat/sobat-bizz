<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('purchase_order')
                    ->tabs([
                        Tab::make(__('resources/purchase_order.tabs.summary'))
                            ->icon('heroicon-o-shopping-cart')
                            ->schema([
                                Section::make(__('resources/purchase_order.sections.information'))
                                    ->schema([
                                        TextEntry::make('po_number')->label(__('common.fields.number')),
                                        TextEntry::make('supplier.company_name')->label(__('common.fields.supplier')),
                                        TextEntry::make('po_date')->label(__('common.fields.po_date'))->date('d M Y'),
                                        TextEntry::make('expected_delivery_date')->label(__('resources/purchase_order.fields.expected_arrival_short'))->date('d M Y')->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('currency')->label(__('common.fields.currency')),
                                        TextEntry::make('payment_terms')->label(__('documents.fields.payment_terms'))->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),
                                Section::make(__('resources/purchase_order.sections.value_summary'))
                                    ->schema([
                                        TextEntry::make('subtotal')->label(__('common.fields.subtotal'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('discount_amount')->label(__('common.fields.discount_amount'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('tax_amount')->label(__('common.fields.tax'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('grand_total')
                                            ->label(__('common.fields.grand_total'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state))
                                            ->weight('bold'),
                                    ])
                                    ->columns(4),
                            ]),

                        Tab::make(__('resources/purchase_order.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/purchase_order.sections.items')),
                            ]),

                        Tab::make(__('resources/purchase_order.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/purchase_order.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/purchase_order.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                    ]),
                                Section::make(__('resources/purchase_order.sections.notes'))
                                    ->schema([
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
