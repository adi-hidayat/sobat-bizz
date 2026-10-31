<?php

namespace App\Filament\Schemas;

use App\Support\Format;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

final class ItemsInfolist
{
    public static function section(?string $title = null): Section
    {
        $title ??= __('common.sections.items');

        return Section::make($title)
            ->schema([
                RepeatableEntry::make('items')
                    ->hiddenLabel()
                    ->placeholder(__('common.placeholders.empty_items'))
                    ->schema([
                        TextEntry::make('sku')->label(__('common.fields.sku'))->placeholder('-'),
                        TextEntry::make('name')->label(__('common.fields.name'))->weight('bold'),
                        TextEntry::make('quantity')
                            ->label(__('common.fields.quantity'))
                            ->formatStateUsing(fn ($state): string => Format::quantity($state)),
                        TextEntry::make('unit')->label(__('common.fields.unit'))->placeholder('-'),
                        TextEntry::make('unit_price')
                            ->label(__('common.fields.unit_price'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('discount_amount')
                            ->label(__('common.fields.discount'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('tax_amount')
                            ->label(__('common.fields.tax'))
                            ->formatStateUsing(fn ($state): string => Format::money($state)),
                        TextEntry::make('total')
                            ->label(__('common.fields.line_total'))
                            ->formatStateUsing(fn ($state): string => Format::money($state))
                            ->weight('bold'),
                    ])
                    ->columns(8),
            ]);
    }
}
