<?php

namespace App\Filament\Resources\Taxes\Tables;

use App\Enums\TaxType;
use App\Filament\Actions\TableActions;
use App\Models\Tax;
use App\Support\Format;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TaxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources/tax.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('common.fields.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('common.fields.type'))
                    ->badge(),
                TextColumn::make('value')
                    ->label(__('common.fields.value'))
                    ->formatStateUsing(fn (string $state, Tax $record): string => $record->type === TaxType::Percentage
                        ? Format::quantity($state).'%'
                        : Format::money($state))
                    ->sortable(),
                TextColumn::make('is_withholding')
                    ->label(__('resources/tax.fields.withholding'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.boolean.yes') : __('resources/tax.boolean.no'))
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('is_default')
                    ->label(__('common.fields.is_default'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.boolean.yes') : __('resources/tax.boolean.no'))
                    ->color(fn (bool $state): string => $state ? 'info' : 'gray'),
                TextColumn::make('sort_order')
                    ->label(__('common.fields.sort_order'))
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/tax.status.active') : __('resources/tax.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('common.fields.type'))
                    ->options(TaxType::class),
                TernaryFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->placeholder(__('resources/tax.filters.all_statuses'))
                    ->trueLabel(__('resources/tax.status.active'))
                    ->falseLabel(__('resources/tax.status.inactive')),
                TernaryFilter::make('is_withholding')
                    ->label(__('resources/tax.fields.is_withholding'))
                    ->placeholder(__('resources/tax.filters.all'))
                    ->trueLabel(__('resources/tax.boolean.yes'))
                    ->falseLabel(__('resources/tax.boolean.no')),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    EditAction::make()
                        ->modal()
                        ->slideOver()
                        ->modalWidth(Width::ThreeExtraLarge),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
