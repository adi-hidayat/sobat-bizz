<?php

namespace App\Filament\Resources\DeliveryOrders\Pages;

use App\Enums\DeliveryOrderStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Resources\DeliveryOrders\DeliveryOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliveryOrder extends ViewRecord
{
    protected static string $resource = DeliveryOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DocumentActions::downloadPdf(),
            DocumentActions::sendEmail(),
            DocumentActions::createInvoiceFromDeliveryOrder(),
            DocumentActions::changeStatus(
                DeliveryOrderStatus::class,
                'update_delivery_order',
                DocumentActions::validateQuantities(),
            ),
        ];
    }
}
