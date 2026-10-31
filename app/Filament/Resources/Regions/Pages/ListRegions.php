<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Resources\Regions\RegionData;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

abstract class ListRegions extends ListRecords
{
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modal()
                ->slideOver()
                ->modalWidth(Width::ThreeExtraLarge)
                ->mutateDataUsing(fn (array $data): array => RegionData::prepare($data, static::getResource()::level())),
        ];
    }
}
