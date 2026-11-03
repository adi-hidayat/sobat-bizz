<?php

namespace App\Filament\Resources\LeadActivityTypes\Pages;

use App\Filament\Resources\LeadActivityTypes\LeadActivityTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListLeadActivityTypes extends ListRecords
{
    protected static string $resource = LeadActivityTypeResource::class;

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
