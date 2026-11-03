<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\Actions\LeadViewModeActions;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    public function mount(): void
    {
        parent::mount();

        session(['leads.view_mode' => 'table']);
    }

    protected function getHeaderActions(): array
    {
        return [
            LeadViewModeActions::make('table'),
            CreateAction::make(),
        ];
    }
}
