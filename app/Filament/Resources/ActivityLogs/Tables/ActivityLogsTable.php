<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Filament\Actions\TableActions;
use App\Models\ActivityLog;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('common.fields.time'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('common.fields.activity'))
                    ->formatStateUsing(fn (ActivityLog $record): string => $record->subject_type_label.' · '.$record->event_label)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('event')
                    ->label(__('common.fields.event'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (ActivityLog $record): string => $record->event_label),
                TextColumn::make('subject_type')
                    ->label(__('common.fields.document'))
                    ->formatStateUsing(fn (ActivityLog $record): string => $record->subject_type_label),
                TextColumn::make('subject_id')
                    ->label(__('resources/activity_log.fields.subject_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('causer.name')
                    ->label(__('common.fields.by'))
                    ->placeholder(__('common.placeholders.system')),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label(__('common.fields.event'))
                    ->options(fn (): array => ActivityLog::query()
                        ->distinct()
                        ->pluck('event', 'event')
                        ->mapWithKeys(fn (string $event): array => [
                            $event => Lang::has('resources/activity_log.events.'.$event)
                                ? __('resources/activity_log.events.'.$event)
                                : $event,
                        ])
                        ->all()),
                SelectFilter::make('subject_type')
                    ->label(__('common.fields.document'))
                    ->options(fn (): array => ActivityLog::query()
                        ->distinct()
                        ->pluck('subject_type', 'subject_type')
                        ->mapWithKeys(fn (string $value): array => [
                            $value => Lang::has('resources/activity_log.subjects.'.$value)
                                ? __('resources/activity_log.subjects.'.$value)
                                : str($value)->headline()->toString(),
                        ])
                        ->all()),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}
