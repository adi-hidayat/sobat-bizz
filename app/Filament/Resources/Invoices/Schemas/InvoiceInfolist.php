<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('invoice')
                    ->tabs([
                        Tab::make(__('resources/invoice.tabs.summary'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make(__('resources/invoice.sections.information'))
                                    ->schema([
                                        TextEntry::make('invoice_number')->label(__('common.fields.number')),
                                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                                        TextEntry::make('invoice_date')->label(__('common.fields.date'))->date('d M Y'),
                                        TextEntry::make('due_date')->label(__('common.fields.due_date'))->date('d M Y'),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('payment_terms')->label(__('resources/invoice.fields.payment_terms_short'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ])
                                    ->columns(3),
                                Section::make(__('resources/invoice.sections.value'))
                                    ->schema([
                                        TextEntry::make('subtotal')->label(__('common.fields.subtotal'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('discount_amount')->label(__('common.fields.discount_amount'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('tax_amount')->label(__('common.fields.tax'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('grand_total')
                                            ->label(__('common.fields.grand_total'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state))
                                            ->weight('bold'),
                                        TextEntry::make('amount_paid')
                                            ->label(__('resources/invoice.fields.amount_paid'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state))
                                            ->color('success'),
                                        TextEntry::make('outstanding_balance')
                                            ->label(__('resources/invoice.fields.outstanding_balance'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state))
                                            ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/invoice.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/invoice.sections.items')),
                            ]),

                        Tab::make(__('resources/invoice.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/invoice.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/invoice.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
