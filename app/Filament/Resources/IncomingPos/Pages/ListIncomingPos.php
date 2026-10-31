<?php

namespace App\Filament\Resources\IncomingPos\Pages;

use App\Filament\Resources\IncomingPos\IncomingPoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomingPos extends ListRecords
{
    protected static string $resource = IncomingPoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
