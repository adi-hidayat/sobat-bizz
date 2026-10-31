<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function afterCreate(): void
    {
        if (filled($this->record->assigned_to)) {
            $this->record->salesTeam()->detach($this->record->assigned_to);
        }
    }
}
