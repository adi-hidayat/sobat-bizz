<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/product.sections.data'))
                    ->schema([
                        TextEntry::make('sku')->label(__('resources/product.fields.sku')),
                        TextEntry::make('name')->label(__('common.fields.name')),
                        TextEntry::make('type')->label(__('common.fields.type'))->badge(),
                        TextEntry::make('category.name')->label(__('resources/product.fields.category'))->placeholder(__('common.placeholders.none')),
                        TextEntry::make('unit')->label(__('common.fields.unit')),
                        TextEntry::make('default_price')
                            ->label(__('resources/product.fields.default_price'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                        TextEntry::make('description')->label(__('common.fields.description'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
