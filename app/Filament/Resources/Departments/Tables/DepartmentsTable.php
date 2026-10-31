<?php

namespace App\Filament\Resources\Departments\Tables;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources/department.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('staff_count')
                    ->label(__('resources/department.fields.staff_count'))
                    ->counts('staff')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('common.fields.sort_order'))
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/department.status.active') : __('resources/department.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->placeholder(__('resources/department.filters.all_statuses'))
                    ->trueLabel(__('resources/department.status.active'))
                    ->falseLabel(__('resources/department.status.inactive')),
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
