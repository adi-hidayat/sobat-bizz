<?php

namespace App\Filament\Resources\CrmJobTitles\Tables;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CrmJobTitlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources/crm_job_title.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->label(__('resources/crm_job_title.fields.leads_count'))
                    ->counts('leads')
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/crm_job_title.status.active') : __('resources/crm_job_title.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->placeholder(__('resources/crm_job_title.filters.all_statuses'))
                    ->trueLabel(__('resources/crm_job_title.status.active'))
                    ->falseLabel(__('resources/crm_job_title.status.inactive')),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}
