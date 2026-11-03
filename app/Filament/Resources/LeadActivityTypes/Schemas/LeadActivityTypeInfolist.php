<?php

namespace App\Filament\Resources\LeadActivityTypes\Schemas;

use App\Enums\LeadActivityGroup;
use App\Models\LeadActivityType;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeadActivityTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key')
                    ->label(__('resources/lead_activity_type.fields.key')),
                TextEntry::make('name')
                    ->label(__('common.fields.name')),
                TextEntry::make('group')
                    ->label(__('resources/lead_activity_type.fields.group'))
                    ->badge()
                    ->formatStateUsing(fn (?LeadActivityGroup $state): string => $state?->getLabel() ?? '-'),
                TextEntry::make('sort_order')
                    ->label(__('resources/lead_activity_type.fields.sort_order')),
                IconEntry::make('is_active')
                    ->label(__('resources/lead_activity_type.fields.is_active'))
                    ->boolean(),
                TextEntry::make('activities_count')
                    ->label(__('resources/lead_activity_type.fields.activities_count'))
                    ->state(fn (LeadActivityType $record): int => $record->activities()->count()),
                TextEntry::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y H:i'),
            ]);
    }
}
