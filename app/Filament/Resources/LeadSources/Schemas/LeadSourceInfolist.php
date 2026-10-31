<?php

namespace App\Filament\Resources\LeadSources\Schemas;

use App\Models\LeadSource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeadSourceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/lead_source.fields.name')),
                TextEntry::make('leads_count')
                    ->label(__('resources/lead_source.fields.leads_count'))
                    ->state(fn (LeadSource $record): int => $record->leads()->count()),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/lead_source.status.active') : __('resources/lead_source.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
