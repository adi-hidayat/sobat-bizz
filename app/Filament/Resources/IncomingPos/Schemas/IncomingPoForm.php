<?php

namespace App\Filament\Resources\IncomingPos\Schemas;

use App\Enums\IncomingPoStatus;
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

class IncomingPoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('incoming_po')
                    ->tabs([
                        Tab::make(__('resources/incoming_po.tabs.information'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Section::make(__('resources/incoming_po.sections.information'))
                                    ->schema([
                                        TextInput::make('incoming_po_number')
                                            ->label(__('resources/incoming_po.fields.incoming_po_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        TextInput::make('customer_po_number')
                                            ->label(__('common.fields.customer_po_number'))
                                            ->maxLength(100),
                                        Select::make('customer_id')
                                            ->label(__('common.fields.customer'))
                                            ->relationship('customer', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(IncomingPoStatus::class)
                                            ->default(IncomingPoStatus::Draft)
                                            ->required(),
                                        DatePicker::make('po_date')
                                            ->label(__('common.fields.po_date'))
                                            ->default(now())
                                            ->required(),
                                        DatePicker::make('expected_delivery_date')
                                            ->label(__('common.fields.expected_delivery_date')),
                                        TextInput::make('currency')
                                            ->label(__('common.fields.currency'))
                                            ->default('IDR')
                                            ->required()
                                            ->maxLength(3),
                                        TextInput::make('payment_terms')
                                            ->label(__('documents.fields.payment_terms'))
                                            ->maxLength(255),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/incoming_po.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/incoming_po.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/incoming_po.tabs.summary'))
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
