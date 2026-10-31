<?php

namespace App\Actions;

use App\Enums\PurchaseOrderStatus;
use App\Models\IncomingPo;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConvertIncomingPoToPurchaseOrder
{
    public function __construct(private readonly DocumentLinker $linker) {}

    /**
     * PO internal untuk pengadaan dari supplier. Tidak wajib untuk transaksi
     * jasa (PRD 4.6).
     *
     * @param  array<string, mixed>  $overrides
     */
    public function handle(IncomingPo $incomingPo, Supplier $supplier, array $overrides = []): PurchaseOrder
    {
        if ($incomingPo->items()->doesntExist()) {
            throw new InvalidArgumentException(__('documents.errors.incoming_po_has_no_items'));
        }

        return DB::transaction(function () use ($incomingPo, $supplier, $overrides): PurchaseOrder {
            $purchaseOrder = PurchaseOrder::query()->create(array_merge([
                'supplier_id' => $supplier->getKey(),
                'po_date' => now()->toDateString(),
                'expected_delivery_date' => $incomingPo->expected_delivery_date?->toDateString()
                    ?? now()->addDays(14)->toDateString(),
                'currency' => $incomingPo->currency,
                'payment_terms' => $supplier->payment_term_days.' hari',
                'notes' => $incomingPo->notes,
                'status' => PurchaseOrderStatus::Draft,
            ], $overrides));

            $incomingPo->items()->get()->each(function ($item) use ($purchaseOrder): void {
                $target = $purchaseOrder->items()->create(ItemSnapshot::from($item));

                $this->linker->linkItem($item, $target, (float) $item->quantity);
            });

            $purchaseOrder->recalculateTotals();

            $this->linker->link($incomingPo, $purchaseOrder);

            $incomingPo->logActivity('procurement_created', 'procurement PO created', [
                'purchase_order_id' => $purchaseOrder->getKey(),
                'purchase_order_number' => $purchaseOrder->po_number,
            ]);

            return $purchaseOrder;
        });
    }
}
