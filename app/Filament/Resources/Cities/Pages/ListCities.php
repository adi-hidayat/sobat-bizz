<?php

namespace App\Filament\Resources\Cities\Pages;

use App\Filament\Resources\Cities\CityResource;
use App\Filament\Resources\Regions\Pages\ListRegions;

class ListCities extends ListRegions
{
    protected static string $resource = CityResource::class;
}
