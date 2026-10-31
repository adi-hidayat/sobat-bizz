<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Filament\Actions\InvoiceActions;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('invoice_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('due_date')
                    ->label(__('common.fields.due_date'))
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record): ?string => $record->due_date?->isPast() && (float) $record->outstanding_balance > 0 ? 'danger' : null),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('grand_total')->label(__('common.fields.total'))->money('IDR')->sortable(),
                TextColumn::make('outstanding_balance')->label(__('common.fields.outstanding_balance'))->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(InvoiceStatus::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    InvoiceActions::recordPayment(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('invoice_date', 'desc');
    }
}
