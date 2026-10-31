<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/product_category.fields.name')),
                TextEntry::make('description')->label(__('common.fields.description'))->placeholder(__('common.placeholders.none')),
                TextEntry::make('products_count')->label(__('resources/product_category.fields.products_count'))->state(fn ($record): int => $record->products()->count()),
            ]);
    }
}
