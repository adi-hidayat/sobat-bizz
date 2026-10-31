<?php

namespace App\Filament\Resources\Villages\Pages;

use App\Filament\Resources\Regions\Pages\ListRegions;
use App\Filament\Resources\Villages\VillageResource;

class ListVillages extends ListRegions
{
    protected static string $resource = VillageResource::class;
}
