<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/payment.sections.data'))
                    ->schema([
                        TextEntry::make('payment_number')->label(__('common.fields.number')),
                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                        TextEntry::make('payment_date')->label(__('common.fields.date'))->date('d M Y'),
                        TextEntry::make('method')->label(__('common.fields.method'))->badge(),
                        TextEntry::make('amount')
                            ->label(__('common.fields.amount'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('allocated_amount')
                            ->label(__('resources/payment.fields.allocated_amount'))
                            ->state(fn ($record): string => Format::money($record->allocatedAmount())),
                        TextEntry::make('unallocated_amount')
                            ->label(__('resources/payment.fields.unallocated_amount'))
                            ->state(fn ($record): string => Format::money($record->unallocatedAmount())),
                        TextEntry::make('reference')->label(__('common.fields.reference'))->placeholder(__('common.placeholders.none')),
                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
