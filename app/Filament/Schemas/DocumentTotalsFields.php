<?php

namespace App\Filament\Schemas;

use App\Support\Format;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

final class DocumentTotalsFields
{
    public static function section(?string $title = null): Section
    {
        $title ??= __('common.sections.summary');

        return Section::make($title)
            ->schema([
                TextInput::make('subtotal')
                    ->label(__('common.fields.subtotal'))
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state): string => Format::money($state)),
                TextInput::make('discount_amount')
                    ->label(__('common.fields.discount_document'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('Rp'),
                TextInput::make('tax_amount')
                    ->label(__('common.fields.tax_amount'))
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state): string => Format::money($state)),
                TextInput::make('grand_total')
                    ->label(__('common.fields.grand_total'))
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state): string => Format::money($state)),
            ])
            ->columns(4);
    }
}
