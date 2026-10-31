<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
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

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label(__('resources/product.fields.sku'))->searchable()->sortable(),
                TextColumn::make('name')->label(__('common.fields.name'))->searchable()->sortable(),
                TextColumn::make('type')->label(__('common.fields.type'))->badge(),
                TextColumn::make('category.name')->label(__('resources/product.fields.category'))->toggleable(),
                TextColumn::make('unit')->label(__('common.fields.unit')),
                TextColumn::make('default_price')->label(__('resources/product.fields.default_price'))->money('IDR')->sortable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('common.fields.type'))->options(ProductType::class),
                SelectFilter::make('status')->label(__('common.fields.status'))->options(ProductStatus::class),
                SelectFilter::make('product_category_id')
                    ->label(__('resources/product.fields.category'))
                    ->relationship('category', 'name'),
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
            ->defaultSort('name');
    }
}
