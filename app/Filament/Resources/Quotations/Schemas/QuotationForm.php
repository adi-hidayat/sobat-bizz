<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Enums\QuotationStatus;
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

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('quotation')
                    ->tabs([
                        Tab::make(__('resources/quotation.tabs.information'))
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Section::make(__('resources/quotation.sections.information'))
                                    ->schema([
                                        TextInput::make('quotation_number')
                                            ->label(__('resources/quotation.fields.quotation_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('customer_id')
                                            ->label(__('common.fields.customer'))
                                            ->relationship('customer', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('quotation_date')
                                            ->label(__('common.fields.quotation_date'))
                                            ->default(now())
                                            ->required(),
                                        DatePicker::make('valid_until')
                                            ->label(__('common.fields.valid_until')),
                                        TextInput::make('version')
                                            ->label(__('common.fields.version'))
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                        TextInput::make('currency')
                                            ->label(__('common.fields.currency'))
                                            ->default('IDR')
                                            ->required()
                                            ->maxLength(3),
                                        TextInput::make('payment_terms')
                                            ->label(__('documents.fields.payment_terms'))
                                            ->maxLength(255),
                                        TextInput::make('delivery_terms')
                                            ->label(__('resources/quotation.fields.delivery_terms'))
                                            ->maxLength(255),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(QuotationStatus::class)
                                            ->default(QuotationStatus::Draft)
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Section::make(__('resources/quotation.sections.description'))
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('terms_and_conditions')
                                            ->label(__('common.fields.terms_and_conditions'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('resources/quotation.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/quotation.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/quotation.tabs.summary'))
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
