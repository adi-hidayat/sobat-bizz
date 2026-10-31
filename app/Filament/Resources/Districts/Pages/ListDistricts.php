<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use App\Filament\Resources\Regions\Pages\ListRegions;

class ListDistricts extends ListRegions
{
    protected static string $resource = DistrictResource::class;
}
