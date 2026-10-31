<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Enums\SupplierStatus;
use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier_code')->label(__('common.fields.code'))->searchable()->sortable(),
                TextColumn::make('company_name')->label(__('resources/supplier.fields.company'))->searchable()->sortable(),
                TextColumn::make('contact_person')->label(__('resources/supplier.fields.contact_person'))->searchable()->toggleable(),
                TextColumn::make('email')->label(__('common.fields.email'))->searchable()->toggleable(),
                TextColumn::make('phone')->label(__('common.fields.phone'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('payment_term_days')->label(__('resources/supplier.fields.payment_term'))->suffix(__('resources/supplier.fields.days_suffix')),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(SupplierStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('company_name');
    }
}
