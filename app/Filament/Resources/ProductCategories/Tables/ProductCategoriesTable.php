<?php

namespace App\Filament\Resources\ProductCategories\Tables;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('common.fields.name'))->searchable()->sortable(),
                TextColumn::make('products_count')->label(__('resources/product_category.fields.products'))->counts('products'),
                TextColumn::make('description')->label(__('common.fields.description'))->limit(60)->toggleable(),
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
                ]),
            ])
            ->defaultSort('name');
    }
}
