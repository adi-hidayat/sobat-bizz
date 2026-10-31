<?php

namespace App\Filament\Resources\LeadRatings\Pages;

use App\Filament\Resources\LeadRatings\LeadRatingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListLeadRatings extends ListRecords
{
    protected static string $resource = LeadRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modal()
                ->slideOver()
                ->modalWidth(Width::ThreeExtraLarge),
        ];
    }
}
