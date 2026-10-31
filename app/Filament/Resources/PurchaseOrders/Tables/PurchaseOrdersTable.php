<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use App\Enums\PurchaseOrderStatus;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('supplier.company_name')->label(__('common.fields.supplier'))->searchable()->sortable(),
                TextColumn::make('po_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('expected_delivery_date')->label(__('resources/purchase_order.fields.expected_arrival_short'))->date('d M Y')->toggleable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('grand_total')->label(__('common.fields.grand_total'))->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(PurchaseOrderStatus::class),
                SelectFilter::make('supplier_id')->label(__('common.fields.supplier'))->relationship('supplier', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (PurchaseOrder $record): string => PurchaseOrderResource::getUrl('view', ['record' => $record])),
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
            ->defaultSort('po_date', 'desc');
    }
}
