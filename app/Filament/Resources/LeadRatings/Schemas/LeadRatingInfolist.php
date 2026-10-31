<?php

namespace App\Filament\Resources\LeadRatings\Schemas;

use App\Models\LeadRating;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeadRatingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('common.fields.name')),
                TextEntry::make('color')
                    ->label(__('resources/lead_rating.fields.color'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('resources/lead_rating.colors.'.$state))
                    ->color(fn (LeadRating $record): string => $record->color),
                TextEntry::make('leads_count')
                    ->label(__('resources/lead_rating.fields.leads_count'))
                    ->state(fn (LeadRating $record): int => $record->leads()->count()),
                TextEntry::make('description')
                    ->label(__('common.fields.description'))
                    ->placeholder(__('common.placeholders.none'))
                    ->columnSpanFull(),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
