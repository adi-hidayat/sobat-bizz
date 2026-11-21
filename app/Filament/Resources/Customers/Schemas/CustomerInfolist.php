<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/customer.sections.detail'))
                    ->schema([
                        TextEntry::make('customer_code')->label(__('common.fields.code')),
                        TextEntry::make('company_name')->label(__('common.fields.company_name')),
                        TextEntry::make('customer_type')->label(__('common.fields.type'))->badge(),
                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                        TextEntry::make('npwp')->label(__('common.fields.npwp'))->placeholder(__('common.placeholders.none')),
                        TextEntry::make('lead.company_name')->label(__('resources/customer.fields.origin_lead'))->placeholder(__('common.placeholders.none')),
                        TextEntry::make('payment_term_days')->label(__('resources/customer.fields.payment_term')),
                        TextEntry::make('credit_limit')
                            ->label(__('common.fields.credit_limit'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('outstanding_receivables')
                            ->label(__('resources/customer.fields.outstanding_receivables'))
                            ->state(fn ($record): string => Format::money($record->outstandingReceivables())),
                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
