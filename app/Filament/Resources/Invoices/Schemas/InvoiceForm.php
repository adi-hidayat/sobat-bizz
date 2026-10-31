<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use App\Filament\Schemas\DocumentTotalsFields;
use App\Filament\Schemas\RenderItem;
use App\Support\Format;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('invoice')
                    ->tabs([
                        Tab::make(__('resources/invoice.tabs.information'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make(__('resources/invoice.sections.information'))
                                    ->schema([
                                        TextInput::make('invoice_number')
                                            ->label(__('resources/invoice.fields.invoice_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('customer_id')
                                            ->label(__('common.fields.customer'))
                                            ->relationship('customer', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('invoice_date')
                                            ->label(__('common.fields.invoice_date'))
                                            ->default(now())
                                            ->required(),
                                        DatePicker::make('due_date')
                                            ->label(__('common.fields.due_date'))
                                            ->default(now()->addDays(30))
                                            ->required(),
                                        TextInput::make('payment_terms')
                                            ->label(__('documents.fields.payment_terms'))
                                            ->maxLength(255),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(InvoiceStatus::class)
                                            ->default(InvoiceStatus::Draft)
                                            ->required(),
                                        TextInput::make('currency')
                                            ->label(__('common.fields.currency'))
                                            ->default('IDR')
                                            ->required()
                                            ->maxLength(3),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/invoice.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/invoice.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/invoice.tabs.summary'))
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                DocumentTotalsFields::section(),
                                Section::make(__('resources/invoice.sections.payment'))
                                    ->schema([
                                        TextInput::make('amount_paid')
                                            ->label(__('resources/invoice.fields.amount_paid'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextInput::make('outstanding_balance')
                                            ->label(__('resources/invoice.fields.outstanding_balance'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
