<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Enums\PurchaseOrderStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            DocumentActions::changeStatus(PurchaseOrderStatus::class, 'update_purchase_order'),
        ];
    }
}
