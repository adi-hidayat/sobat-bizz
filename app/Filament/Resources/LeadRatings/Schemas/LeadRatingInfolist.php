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
                    ->formatStateUsing(fn (string $state, LeadRating $record): string => view(
                        'filament.partials.color-badge',
                        [
                            'label' => $state,
                            'background' => $record->color,
                            'textColor' => $record->text_color,
                        ],
                    )->render())
                    ->html(),
                TextEntry::make('text_color')
                    ->label(__('resources/lead_rating.fields.text_color'))
                    ->formatStateUsing(fn (string $state): string => view(
                        'filament.partials.color-badge',
                        [
                            'label' => $state,
                            'background' => $state,
                            'textColor' => '#ffffff',
                        ],
                    )->render())
                    ->html(),
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
