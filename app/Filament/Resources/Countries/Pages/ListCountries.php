<?php

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Countries\CountryResource;
use App\Filament\Resources\Regions\Pages\ListRegions;

class ListCountries extends ListRegions
{
    protected static string $resource = CountryResource::class;
}
