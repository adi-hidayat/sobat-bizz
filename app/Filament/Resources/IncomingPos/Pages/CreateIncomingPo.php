<?php

namespace App\Filament\Resources\IncomingPos\Pages;

use App\Filament\Resources\IncomingPos\IncomingPoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncomingPo extends CreateRecord
{
    protected static string $resource = IncomingPoResource::class;
}
