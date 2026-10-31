<?php

namespace App\Filament\Resources\DeliveryOrders\Tables;

use App\Enums\DeliveryOrderStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\DeliveryOrders\DeliveryOrderResource;
use App\Models\DeliveryOrder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliveryOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('do_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('delivery_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('delivery_method')->label(__('common.fields.method'))->toggleable(),
                TextColumn::make('received_by')->label(__('common.fields.received_by'))->placeholder(__('common.placeholders.none'))->toggleable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(DeliveryOrderStatus::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (DeliveryOrder $record): string => DeliveryOrderResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    DocumentActions::createInvoiceFromDeliveryOrder(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('delivery_date', 'desc');
    }
}
