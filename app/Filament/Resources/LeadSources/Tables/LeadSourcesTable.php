<?php

namespace App\Filament\Resources\LeadSources\Tables;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LeadSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources/lead_source.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->label(__('resources/lead_source.fields.leads_count'))
                    ->counts('leads')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('common.fields.sort_order'))
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/lead_source.status.active') : __('resources/lead_source.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->placeholder(__('resources/lead_source.filters.all_statuses'))
                    ->trueLabel(__('resources/lead_source.status.active'))
                    ->falseLabel(__('resources/lead_source.status.inactive')),
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
