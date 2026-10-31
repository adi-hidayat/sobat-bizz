<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use App\Services\Documents\OutstandingQuantityResolver;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateInvoiceFromDocument
{
    public function __construct(
        private readonly DocumentLinker $linker,
        private readonly OutstandingQuantityResolver $resolver,
    ) {}

    /**
     * Invoice dapat dibuat dari Incoming PO (langsung) atau dari Delivery Order.
     * Kuantitas yang sudah ditagihkan sebelumnya tidak diulang (partial invoicing).
     *
     * @param  array<string, mixed>  $overrides
     */
    public function handle(IncomingPo|DeliveryOrder $source, array $overrides = []): Invoice
    {
        $rows = $this->outstandingRows($source);

        if ($rows === []) {
            throw new InvalidArgumentException(__('documents.errors.no_remaining_invoice_quantity'));
        }

        return DB::transaction(function () use ($source, $overrides, $rows): Invoice {
            $customer = $source->customer;

            $invoice = Invoice::query()->create(array_merge([
                'customer_id' => $source->customer_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays($customer?->payment_term_days ?? 30)->toDateString(),
                'payment_terms' => ($customer?->payment_term_days ?? 30).' hari',
                'currency' => 'IDR',
                'status' => InvoiceStatus::Draft,
            ], $overrides));

            collect($rows)->each(function (array $row) use ($invoice): void {
                /** @var Item $item */
                $item = $row['item'];

                $target = $invoice->items()->create(ItemSnapshot::from($item, [
                    'quantity' => $row['outstanding'],
                ]));

                $this->linker->linkItem($item, $target, $row['outstanding']);
            });

            $invoice->recalculateTotals();

            $this->linker->link($source, $invoice);

            $source->logActivity('invoice_created', 'invoice created', [
                'invoice_id' => $invoice->getKey(),
                'invoice_number' => $invoice->invoice_number,
            ]);

            return $invoice;
        });
    }

    /**
     * @return list<array{item: Item, outstanding: float}>
     */
    private function outstandingRows(IncomingPo|DeliveryOrder $source): array
    {
        return $source->items()->get()
            ->map(fn ($item): array => [
                'item' => $item,
                'outstanding' => $this->resolver->outstanding($item, [Invoice::class]),
            ])
            ->filter(fn (array $row): bool => $row['outstanding'] > 0)
            ->values()
            ->all();
    }
}
