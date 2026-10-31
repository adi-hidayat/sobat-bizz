<?php

namespace App\Filament\Resources\IncomingPos\Pages;

use App\Filament\Resources\IncomingPos\IncomingPoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIncomingPo extends EditRecord
{
    protected static string $resource = IncomingPoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
