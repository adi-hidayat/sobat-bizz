<?php

namespace App\Filament\Resources\CrmIndustries\Schemas;

use App\Models\CrmIndustry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CrmIndustryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/crm_industry.fields.name')),
                TextEntry::make('leads_count')
                    ->label(__('resources/crm_industry.fields.leads_count'))
                    ->state(fn (CrmIndustry $record): int => $record->leads()->count()),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/crm_industry.status.active') : __('resources/crm_industry.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
