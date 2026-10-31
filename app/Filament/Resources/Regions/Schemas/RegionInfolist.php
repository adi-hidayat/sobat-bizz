<?php

namespace App\Filament\Resources\Regions\Schemas;

use App\Models\Region;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class RegionInfolist
{
    public static function configure(Schema $schema, string $level): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(RegionForm::nameLabel($level)),
                TextEntry::make('code')->label(__('common.fields.code'))->placeholder('-'),
                TextEntry::make('country.name')
                    ->label(__('common.fields.country'))
                    ->placeholder('-')
                    ->visible($level !== Region::LEVEL_COUNTRY),
                TextEntry::make('parent.name')
                    ->label(RegionForm::parentLabel($level))
                    ->placeholder('-')
                    ->visible(in_array($level, [Region::LEVEL_CITY, Region::LEVEL_DISTRICT, Region::LEVEL_VILLAGE], true)),
                TextEntry::make('postal_code')
                    ->label(__('common.fields.postal_code'))
                    ->placeholder('-')
                    ->visible($level === Region::LEVEL_VILLAGE),
                TextEntry::make('latitude')
                    ->label(__('common.fields.latitude'))
                    ->placeholder('-')
                    ->visible(in_array($level, [Region::LEVEL_COUNTRY, Region::LEVEL_STATE, Region::LEVEL_CITY], true)),
                TextEntry::make('longitude')
                    ->label(__('common.fields.longitude'))
                    ->placeholder('-')
                    ->visible(in_array($level, [Region::LEVEL_COUNTRY, Region::LEVEL_STATE, Region::LEVEL_CITY], true)),
                TextEntry::make('source')->label(__('common.fields.source')),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
                TextEntry::make('updated_at')->label(__('common.fields.updated_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
