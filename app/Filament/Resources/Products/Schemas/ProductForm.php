<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/product.sections.data'))
                    ->schema([
                        TextInput::make('sku')
                            ->label(__('resources/product.fields.sku_code'))
                            ->required()
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label(__('common.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label(__('common.fields.type'))
                            ->options(ProductType::class)
                            ->default(ProductType::Product)
                            ->required(),
                        Select::make('product_category_id')
                            ->label(__('resources/product.fields.category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('unit')
                            ->label(__('common.fields.unit'))
                            ->default('pcs')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('default_price')
                            ->label(__('resources/product.fields.default_price'))
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->required(),
                        Select::make('status')
                            ->label(__('common.fields.status'))
                            ->options(ProductStatus::class)
                            ->default(ProductStatus::Active)
                            ->required(),
                        Textarea::make('description')
                            ->label(__('common.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
