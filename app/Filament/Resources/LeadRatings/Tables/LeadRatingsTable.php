<?php

namespace App\Filament\Resources\LeadRatings\Tables;

use App\Filament\Actions\TableActions;
use App\Models\LeadRating;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label(__('resources/lead_rating.fields.color'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('resources/lead_rating.colors.'.$state))
                    ->color(fn (LeadRating $record): string => $record->color),
                TextColumn::make('description')
                    ->label(__('common.fields.description'))
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('leads_count')
                    ->label(__('resources/lead_rating.fields.leads_count'))
                    ->counts('leads')
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
            ->defaultSort('name');
    }
}
