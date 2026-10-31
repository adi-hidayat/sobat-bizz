<?php

namespace App\Filament\Resources\Invoices\RelationManagers;

use App\Support\Format;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources/invoice.relation_managers.payments.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_number')->label(__('resources/invoice.relation_managers.payments.payment_number'))->searchable(),
                TextColumn::make('payment_date')->label(__('common.fields.date'))->date('d M Y'),
                TextColumn::make('method')->label(__('common.fields.method'))->badge(),
                TextColumn::make('pivot.amount')
                    ->label(__('resources/invoice.relation_managers.payments.allocated'))
                    ->formatStateUsing(fn ($state): string => Format::money($state)),
                TextColumn::make('reference')->label(__('common.fields.reference'))->placeholder(__('common.placeholders.none')),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
