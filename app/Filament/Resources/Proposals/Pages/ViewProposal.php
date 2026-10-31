<?php

namespace App\Filament\Resources\Proposals\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Resources\Proposals\ProposalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProposal extends ViewRecord
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            DocumentActions::convertToQuotation(),
            DocumentActions::changeStatus(ProposalStatus::class, 'update_proposal'),
        ];
    }
}
