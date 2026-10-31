<?php

namespace App\Filament\Resources\IncomingPos\Tables;

use App\Enums\IncomingPoStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\IncomingPos\IncomingPoResource;
use App\Models\IncomingPo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncomingPosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('incoming_po_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer_po_number')->label(__('resources/incoming_po.fields.customer_po_number_short'))->searchable()->toggleable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('po_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('expected_delivery_date')->label(__('resources/incoming_po.fields.expected_delivery_short'))->date('d M Y')->toggleable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('grand_total')->label(__('common.fields.grand_total'))->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(IncomingPoStatus::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (IncomingPo $record): string => IncomingPoResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    DocumentActions::convertToPurchaseOrder(),
                    DocumentActions::convertToDeliveryOrder(),
                    DocumentActions::createInvoiceFromIncomingPo(),
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
