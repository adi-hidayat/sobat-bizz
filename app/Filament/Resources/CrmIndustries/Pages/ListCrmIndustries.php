<?php

namespace App\Filament\Resources\CrmIndustries\Pages;

use App\Filament\Resources\CrmIndustries\CrmIndustryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCrmIndustries extends ListRecords
{
    protected static string $resource = CrmIndustryResource::class;

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
