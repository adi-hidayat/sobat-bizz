<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use App\Support\Format;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('payment_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('method')->label(__('common.fields.method'))->badge(),
                TextColumn::make('amount')->label(__('common.fields.amount'))->money('IDR')->sortable(),
                TextColumn::make('allocated_amount')
                    ->label(__('resources/payment.fields.allocated_amount'))
                    ->state(fn ($record): string => Format::money($record->allocatedAmount()))
                    ->toggleable(),
                TextColumn::make('unallocated_amount')
                    ->label(__('resources/payment.fields.unallocated_amount'))
                    ->state(fn ($record): string => Format::money($record->unallocatedAmount()))
                    ->toggleable(),
                TextColumn::make('reference')->label(__('common.fields.reference'))->placeholder(__('common.placeholders.none'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('method')->label(__('common.fields.method'))->options(PaymentMethod::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Payment $record): string => PaymentResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('payment_date', 'desc');
    }
}
