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
                    ->formatStateUsing(fn (string $state, LeadRating $record): string => view(
                        'filament.partials.color-badge',
                        [
                            'label' => $state,
                            'background' => $record->color,
                            'textColor' => $record->text_color,
                        ],
                    )->render())
                    ->html(),
                TextColumn::make('text_color')
                    ->label(__('resources/lead_rating.fields.text_color'))
                    ->formatStateUsing(fn (string $state): string => view(
                        'filament.partials.color-badge',
                        [
                            'label' => $state,
                            'background' => $state,
                            'textColor' => '#ffffff',
                        ],
                    )->render())
                    ->html()
                    ->toggleable(),
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
