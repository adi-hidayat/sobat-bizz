<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\DeliveryOrders\DeliveryOrderResource;
use App\Models\DeliveryOrder;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrders extends CustomerScopedPage
{
    protected static ?string $slug = 'delivery-orders';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.delivery_orders');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.delivery_orders');
    }

    protected function relationship(): string
    {
        return 'deliveryOrders';
    }

    protected function defaultSort(): string
    {
        return 'delivery_date';
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('do_number')->label(__('pages.customer_detail.columns.number'))->searchable(),
            TextColumn::make('delivery_date')->label(__('pages.customer_detail.columns.date'))->date('d M Y')->sortable(),
            TextColumn::make('delivery_method')->label(__('pages.customer_detail.columns.delivery_method'))->placeholder('-'),
            TextColumn::make('received_by')->label(__('pages.customer_detail.columns.received_by'))->placeholder('-'),
            TextColumn::make('status')->label(__('pages.customer_detail.columns.status'))->badge(),
        ];
    }

    protected function recordUrl(Model $record): ?string
    {
        return $record instanceof DeliveryOrder
            ? DeliveryOrderResource::getUrl('view', ['record' => $record])
            : null;
    }
}
