<?php

namespace App\Filament\Resources\LeadActivityTypes\Tables;

use App\Enums\LeadActivityGroup;
use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadActivityTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('group')
                    ->label(__('resources/lead_activity_type.fields.group'))
                    ->badge()
                    ->formatStateUsing(fn (?LeadActivityGroup $state): string => $state?->getLabel() ?? '-')
                    ->sortable(),
                TextColumn::make('key')
                    ->label(__('resources/lead_activity_type.fields.key'))
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('resources/lead_activity_type.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('resources/lead_activity_type.fields.sort_order'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('activities_count')
                    ->label(__('resources/lead_activity_type.fields.activities_count'))
                    ->counts('activities')
                    ->sortable(),
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
