<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use App\Models\ActivityLog;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('activity_log')
                    ->tabs([
                        Tab::make(__('common.sections.information'))
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make(__('resources/activity_log.sections.detail'))
                                    ->schema([
                                        TextEntry::make('created_at')->label(__('common.fields.time'))->dateTime('d M Y H:i'),
                                        TextEntry::make('event')
                                            ->label(__('common.fields.event'))
                                            ->badge()
                                            ->formatStateUsing(fn (ActivityLog $record): string => $record->event_label),
                                        TextEntry::make('description')
                                            ->label(__('common.fields.activity'))
                                            ->state(fn (ActivityLog $record): string => $record->subject_type_label.' · '.$record->event_label)
                                            ->columnSpanFull(),
                                        TextEntry::make('subject_type')
                                            ->label(__('common.fields.document'))
                                            ->formatStateUsing(fn (ActivityLog $record): string => $record->subject_type_label),
                                        TextEntry::make('subject_id')->label(__('resources/activity_log.fields.document_id'))->placeholder('-'),
                                        TextEntry::make('causer.name')->label(__('common.fields.by'))->placeholder(__('common.placeholders.system')),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/activity_log.tabs.changes'))
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Section::make(__('common.fields.properties'))
                                    ->schema([
                                        TextEntry::make('properties')
                                            ->label('')
                                            ->state(fn ($record): string => $record->properties === null
                                                ? '-'
                                                : json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
                                            ->fontFamily('mono')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
