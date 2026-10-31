<?php

namespace App\Services\Documents;

use App\Enums\IncomingPoStatus;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Item;

final class RefreshFulfillmentStatus
{
    public function __construct(private readonly OutstandingQuantityResolver $resolver) {}

    /**
     * Status Incoming PO mengikuti total kuantitas yang sudah dikirim via DO.
     */
    public function forIncomingPo(IncomingPo $incomingPo): void
    {
        $incomingPo->refresh();

        if ($incomingPo->status === IncomingPoStatus::Draft || $incomingPo->status->isCancelled()) {
            return;
        }

        $items = $incomingPo->items()->get();

        if ($items->isEmpty()) {
            return;
        }

        $total = 0.0;
        $outstanding = 0.0;

        foreach ($items as $item) {
            $total += (float) $item->quantity;
            $outstanding += $this->resolver->outstanding($item, [DeliveryOrder::class]);
        }

        $status = match (true) {
            $outstanding <= 0 => IncomingPoStatus::Fulfilled,
            $outstanding < $total => IncomingPoStatus::PartiallyFulfilled,
            default => $incomingPo->status === IncomingPoStatus::PartiallyFulfilled
                ? IncomingPoStatus::Confirmed
                : $incomingPo->status,
        };

        if ($status !== $incomingPo->status) {
            $incomingPo->forceFill(['status' => $status])->save();
        }
    }

    public function forIncomingPoOfItem(Item $item): void
    {
        if ($item->itemable instanceof DeliveryOrder) {
            $incomingPo = $item->itemable->documentLinksAsTarget->first()?->source;

            if ($incomingPo instanceof IncomingPo) {
                $this->forIncomingPo($incomingPo);
            }
        }
    }

    /**
     * Delivery Order yang seluruh itemnya sudah diterima dapat ditandai selesai.
     */
    public function forDeliveryOrder(DeliveryOrder $deliveryOrder): void
    {
        if ($deliveryOrder->status->isCancelled()) {
            return;
        }

        $incomingPo = $deliveryOrder->documentLinksAsTarget()
            ->with('source')
            ->get()
            ->pluck('source')
            ->first(fn ($source) => $source instanceof IncomingPo);

        if ($incomingPo instanceof IncomingPo) {
            $this->forIncomingPo($incomingPo);
        }
    }
}
