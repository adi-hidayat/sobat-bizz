<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class Invoices extends CustomerScopedPage
{
    protected static ?string $slug = 'invoices';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.invoices');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.invoices');
    }

    protected function relationship(): string
    {
        return 'invoices';
    }

    protected function defaultSort(): string
    {
        return 'invoice_date';
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('invoice_number')->label(__('pages.customer_detail.columns.number'))->searchable(),
            TextColumn::make('invoice_date')->label(__('pages.customer_detail.columns.date'))->date('d M Y')->sortable(),
            TextColumn::make('due_date')->label(__('pages.customer_detail.columns.due_date'))->date('d M Y'),
            TextColumn::make('status')->label(__('pages.customer_detail.columns.status'))->badge(),
            TextColumn::make('grand_total')->label(__('pages.customer_detail.columns.total'))->money('IDR')->sortable(),
            TextColumn::make('outstanding_balance')->label(__('pages.customer_detail.columns.outstanding_balance'))->money('IDR')->sortable(),
        ];
    }

    protected function recordUrl(Model $record): ?string
    {
        return $record instanceof Invoice
            ? InvoiceResource::getUrl('view', ['record' => $record])
            : null;
    }
}
