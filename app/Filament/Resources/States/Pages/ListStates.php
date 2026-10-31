<?php

namespace App\Filament\Resources\States\Pages;

use App\Filament\Resources\Regions\Pages\ListRegions;
use App\Filament\Resources\States\StateResource;

class ListStates extends ListRegions
{
    protected static string $resource = StateResource::class;
}
