<?php

namespace App\Filament\Resources\IncomingPos\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class IncomingPoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('incoming_po')
                    ->tabs([
                        Tab::make(__('resources/incoming_po.tabs.summary'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Section::make(__('resources/incoming_po.sections.information'))
                                    ->schema([
                                        TextEntry::make('incoming_po_number')->label(__('common.fields.number')),
                                        TextEntry::make('customer_po_number')->label(__('resources/incoming_po.fields.customer_po_number_short'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                                        TextEntry::make('po_date')->label(__('common.fields.po_date'))->date('d M Y'),
                                        TextEntry::make('expected_delivery_date')->label(__('resources/incoming_po.fields.expected_delivery_short'))->date('d M Y')->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('currency')->label(__('common.fields.currency')),
                                        TextEntry::make('payment_terms')->label(__('documents.fields.payment_terms'))->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),
                                Section::make(__('resources/incoming_po.sections.value_summary'))
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

                        Tab::make(__('resources/incoming_po.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/incoming_po.sections.items')),
                            ]),

                        Tab::make(__('resources/incoming_po.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/incoming_po.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/incoming_po.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                        TextEntry::make('target_documents')
                                            ->label(__('resources/incoming_po.fields.target_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::targets($record)),
                                    ])
                                    ->columns(2),
                                Section::make(__('resources/incoming_po.sections.notes'))
                                    ->schema([
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
