<?php

namespace App\Filament\Resources\Quotations\Tables;

use App\Enums\QuotationStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('version')->label(__('common.fields.version'))->sortable(),
                TextColumn::make('quotation_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('valid_until')->label(__('common.fields.valid_until'))->date('d M Y')->toggleable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('grand_total')->label(__('common.fields.grand_total'))->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(QuotationStatus::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Quotation $record): string => QuotationResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    DocumentActions::convertToIncomingPo(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('quotation_date', 'desc');
    }
}
