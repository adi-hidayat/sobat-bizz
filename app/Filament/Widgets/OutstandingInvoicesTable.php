<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OutstandingInvoicesTable extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('widgets.outstanding_invoices.heading'))
            ->query(
                Invoice::query()
                    ->whereNotIn('status', [InvoiceStatus::Draft->value, InvoiceStatus::Cancelled->value])
                    ->where('outstanding_balance', '>', 0)
                    ->orderBy('due_date'),
            )
            ->columns([
                TextColumn::make('invoice_number')->label(__('widgets.outstanding_invoices.invoice')),
                TextColumn::make('customer.company_name')->label(__('widgets.outstanding_invoices.customer'))->searchable(),
                TextColumn::make('invoice_date')->label(__('widgets.outstanding_invoices.date'))->date('d M Y'),
                TextColumn::make('due_date')
                    ->label(__('widgets.outstanding_invoices.due_date'))
                    ->date('d M Y')
                    ->color(fn (Invoice $record): ?string => $record->due_date?->isPast() ? 'danger' : null),
                TextColumn::make('grand_total')->label(__('widgets.outstanding_invoices.total'))->money('IDR'),
                TextColumn::make('amount_paid')->label(__('widgets.outstanding_invoices.paid'))->money('IDR'),
                TextColumn::make('outstanding_balance')
                    ->label(__('widgets.outstanding_invoices.outstanding'))
                    ->money('IDR')
                    ->weight('bold'),
                TextColumn::make('status')->label(__('widgets.outstanding_invoices.status'))->badge(),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25]);
    }
}
