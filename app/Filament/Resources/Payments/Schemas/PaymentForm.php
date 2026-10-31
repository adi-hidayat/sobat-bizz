<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/payment.sections.data'))
                    ->schema([
                        TextInput::make('payment_number')
                            ->label(__('resources/payment.fields.payment_number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('common.placeholders.auto_generated')),
                        Select::make('customer_id')
                            ->label(__('common.fields.customer'))
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label(__('common.fields.payment_date'))
                            ->default(now())
                            ->required(),
                        Select::make('method')
                            ->label(__('common.fields.method'))
                            ->options(PaymentMethod::class)
                            ->default(PaymentMethod::BankTransfer)
                            ->required(),
                        TextInput::make('amount')
                            ->label(__('common.fields.amount'))
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('reference')
                            ->label(__('common.fields.reference'))
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->label(__('common.fields.notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
