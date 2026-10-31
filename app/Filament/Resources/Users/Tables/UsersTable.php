<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Actions\TableActions;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('common.fields.name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('common.fields.email'))->searchable(),
                TextColumn::make('job_title')->label(__('common.fields.job_title'))->toggleable(),
                TextColumn::make('roles.name')->label(__('common.fields.roles'))->badge(),
                TextColumn::make('is_active')
                    ->label(__('common.fields.is_active'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state
                        ? __('resources/user.boolean.yes')
                        : __('resources/user.boolean.no'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('common.fields.roles'))
                    ->relationship('roles', 'name'),
                SelectFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->options([
                        1 => __('resources/user.status.active'),
                        0 => __('resources/user.status.inactive'),
                    ]),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->defaultSort('name');
    }
}
