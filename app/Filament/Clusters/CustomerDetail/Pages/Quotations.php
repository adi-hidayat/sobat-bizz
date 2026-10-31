<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class Quotations extends CustomerScopedPage
{
    protected static ?string $slug = 'quotations';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.quotations');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.quotations');
    }

    protected function relationship(): string
    {
        return 'quotations';
    }

    protected function defaultSort(): string
    {
        return 'quotation_date';
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('quotation_number')->label(__('pages.customer_detail.columns.number'))->searchable(),
            TextColumn::make('version')->label(__('pages.customer_detail.columns.version'))->sortable(),
            TextColumn::make('quotation_date')->label(__('pages.customer_detail.columns.date'))->date('d M Y')->sortable(),
            TextColumn::make('valid_until')->label(__('pages.customer_detail.columns.valid_until'))->date('d M Y'),
            TextColumn::make('status')->label(__('pages.customer_detail.columns.status'))->badge(),
            TextColumn::make('grand_total')->label(__('pages.customer_detail.columns.grand_total'))->money('IDR')->sortable(),
        ];
    }

    protected function recordUrl(Model $record): ?string
    {
        return $record instanceof Quotation
            ? QuotationResource::getUrl('view', ['record' => $record])
            : null;
    }
}
