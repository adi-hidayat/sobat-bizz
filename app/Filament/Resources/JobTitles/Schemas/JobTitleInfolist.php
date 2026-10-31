<?php

namespace App\Filament\Resources\JobTitles\Schemas;

use App\Models\JobTitle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JobTitleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/job_title.fields.name')),
                TextEntry::make('users_count')
                    ->label(__('resources/job_title.fields.users_count'))
                    ->state(fn (JobTitle $record): int => $record->users()->count()),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/job_title.status.active') : __('resources/job_title.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
