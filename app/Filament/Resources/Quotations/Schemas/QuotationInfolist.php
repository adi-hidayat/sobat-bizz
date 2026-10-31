<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class QuotationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('quotation')
                    ->tabs([
                        Tab::make(__('resources/quotation.tabs.summary'))
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Section::make(__('resources/quotation.sections.information'))
                                    ->schema([
                                        TextEntry::make('quotation_number')->label(__('common.fields.number')),
                                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                                        TextEntry::make('version')->label(__('common.fields.version')),
                                        TextEntry::make('quotation_date')->label(__('common.fields.date'))->date('d M Y'),
                                        TextEntry::make('valid_until')->label(__('common.fields.valid_until'))->date('d M Y')->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('currency')->label(__('common.fields.currency')),
                                        TextEntry::make('payment_terms')->label(__('documents.fields.payment_terms'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('delivery_terms')->label(__('resources/quotation.fields.delivery_terms'))->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),
                                Section::make(__('resources/quotation.sections.value_summary'))
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

                        Tab::make(__('resources/quotation.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/quotation.sections.items')),
                            ]),

                        Tab::make(__('resources/quotation.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/quotation.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/quotation.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                        TextEntry::make('target_documents')
                                            ->label(__('resources/quotation.fields.target_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::targets($record)),
                                    ])
                                    ->columns(2),
                                Section::make(__('resources/quotation.sections.description'))
                                    ->schema([
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('terms_and_conditions')->label(__('common.fields.terms_and_conditions'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
