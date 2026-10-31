<?php

namespace App\Actions;

use App\Enums\DeliveryOrderStatus;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Item;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use App\Services\Documents\OutstandingQuantityResolver;
use App\Services\Documents\RefreshFulfillmentStatus;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConvertIncomingPoToDeliveryOrder
{
    public function __construct(
        private readonly DocumentLinker $linker,
        private readonly OutstandingQuantityResolver $resolver,
        private readonly RefreshFulfillmentStatus $refreshFulfillmentStatus,
    ) {}

    /**
     * Membuat DO dari sisa kuantitas Incoming PO. Satu Incoming PO dapat
     * menghasilkan beberapa DO (PRD 4.7).
     *
     * @param  array<string, mixed>  $overrides
     */
    public function handle(IncomingPo $incomingPo, array $overrides = []): DeliveryOrder
    {
        $items = $incomingPo->items()->get();

        $outstandingItems = $items->map(fn ($item): array => [
            'item' => $item,
            'outstanding' => $this->resolver->outstanding($item, [DeliveryOrder::class]),
        ])->filter(fn (array $row): bool => $row['outstanding'] > 0);

        if ($outstandingItems->isEmpty()) {
            throw new InvalidArgumentException(__('documents.errors.no_remaining_incoming_po_quantity'));
        }

        return DB::transaction(function () use ($incomingPo, $overrides, $outstandingItems): DeliveryOrder {
            $deliveryOrder = DeliveryOrder::query()->create(array_merge([
                'customer_id' => $incomingPo->customer_id,
                'delivery_date' => now()->toDateString(),
                'shipping_address' => $incomingPo->customer?->shippingAddress()?->full_address,
                'delivery_method' => null,
                'delivery_notes' => null,
                'status' => DeliveryOrderStatus::Draft,
            ], $overrides));

            $outstandingItems->each(function (array $row) use ($deliveryOrder): void {
                /** @var Item $item */
                $item = $row['item'];

                $target = $deliveryOrder->items()->create(ItemSnapshot::from($item, [
                    'quantity' => $row['outstanding'],
                ]));

                $this->linker->linkItem($item, $target, $row['outstanding']);
            });

            $this->linker->link($incomingPo, $deliveryOrder);

            $incomingPo->logActivity('delivery_order_created', 'delivery order created', [
                'delivery_order_id' => $deliveryOrder->getKey(),
                'do_number' => $deliveryOrder->do_number,
            ]);

            $this->refreshFulfillmentStatus->forIncomingPo($incomingPo);

            return $deliveryOrder;
        });
    }
}
