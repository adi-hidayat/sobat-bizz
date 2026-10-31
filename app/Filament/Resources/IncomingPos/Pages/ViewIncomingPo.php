<?php

namespace App\Filament\Resources\IncomingPos\Pages;

use App\Enums\IncomingPoStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Resources\IncomingPos\IncomingPoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIncomingPo extends ViewRecord
{
    protected static string $resource = IncomingPoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            DocumentActions::convertToPurchaseOrder(),
            DocumentActions::convertToDeliveryOrder(),
            DocumentActions::createInvoiceFromIncomingPo(),
            DocumentActions::changeStatus(IncomingPoStatus::class, 'update_incoming_po'),
        ];
    }
}
