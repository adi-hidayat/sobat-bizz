<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\IncomingPos\IncomingPoResource;
use App\Models\IncomingPo;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class IncomingPos extends CustomerScopedPage
{
    protected static ?string $slug = 'incoming-pos';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.incoming_pos');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.incoming_pos');
    }

    protected function relationship(): string
    {
        return 'incomingPos';
    }

    protected function defaultSort(): string
    {
        return 'po_date';
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('incoming_po_number')->label(__('pages.customer_detail.columns.number'))->searchable(),
            TextColumn::make('customer_po_number')->label(__('pages.customer_detail.columns.customer_po_number'))->placeholder('-'),
            TextColumn::make('po_date')->label(__('pages.customer_detail.columns.date'))->date('d M Y')->sortable(),
            TextColumn::make('expected_delivery_date')->label(__('pages.customer_detail.columns.expected_delivery_date'))->date('d M Y'),
            TextColumn::make('status')->label(__('pages.customer_detail.columns.status'))->badge(),
            TextColumn::make('grand_total')->label(__('pages.customer_detail.columns.grand_total'))->money('IDR')->sortable(),
        ];
    }

    protected function recordUrl(Model $record): ?string
    {
        return $record instanceof IncomingPo
            ? IncomingPoResource::getUrl('view', ['record' => $record])
            : null;
    }
}
