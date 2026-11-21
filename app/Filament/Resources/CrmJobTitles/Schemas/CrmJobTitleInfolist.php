<?php

namespace App\Filament\Resources\CrmJobTitles\Schemas;

use App\Models\CrmJobTitle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CrmJobTitleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/crm_job_title.fields.name')),
                TextEntry::make('leads_count')
                    ->label(__('resources/crm_job_title.fields.leads_count'))
                    ->state(fn (CrmJobTitle $record): int => $record->leads()->count()),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/crm_job_title.status.active') : __('resources/crm_job_title.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
