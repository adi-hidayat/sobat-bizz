<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Clusters\LeadDetail\Pages\LeadInformation;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->redirect(LeadInformation::getUrl(['lead' => $record]));
    }
}
