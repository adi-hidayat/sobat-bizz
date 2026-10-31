<?php

namespace App\Filament\Resources\Taxes\Schemas;

use App\Enums\TaxType;
use App\Models\Tax;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/tax.fields.name')),
                TextEntry::make('code')->label(__('common.fields.code')),
                TextEntry::make('type')->label(__('resources/tax.fields.type'))->badge(),
                TextEntry::make('value')
                    ->label(__('common.fields.value'))
                    ->formatStateUsing(fn (string $state, Tax $record): string => $record->type === TaxType::Percentage
                        ? Format::quantity($state).'%'
                        : Format::money($state)),
                TextEntry::make('is_withholding')
                    ->label(__('resources/tax.fields.is_withholding'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.boolean.yes') : __('resources/tax.boolean.no'))
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.status.active') : __('resources/tax.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('is_default')
                    ->label(__('resources/tax.fields.is_default'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.boolean.yes') : __('resources/tax.boolean.no'))
                    ->color(fn (bool $state): string => $state ? 'info' : 'gray'),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('description')->label(__('common.fields.description'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
