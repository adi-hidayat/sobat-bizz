<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Actions\InvoiceActions;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            InvoiceActions::recordPayment(),
            DocumentActions::changeStatus(InvoiceStatus::class, 'update_invoice'),
        ];
    }
}
