<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Enums\QuotationStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuotation extends ViewRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            DocumentActions::convertToIncomingPo(),
            DocumentActions::reviseQuotation(),
            DocumentActions::changeStatus(QuotationStatus::class, 'update_quotation'),
        ];
    }
}
