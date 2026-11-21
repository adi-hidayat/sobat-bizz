<?php

namespace App\Filament\Resources\CrmJobTitles\Pages;

use App\Filament\Resources\CrmJobTitles\CrmJobTitleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCrmJobTitles extends ListRecords
{
    protected static string $resource = CrmJobTitleResource::class;

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
