<?php

namespace App\Actions;

use App\Enums\IncomingPoStatus;
use App\Enums\QuotationStatus;
use App\Models\IncomingPo;
use App\Models\Quotation;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConvertQuotationToIncomingPo
{
    public function __construct(private readonly DocumentLinker $linker) {}

    /**
     * @param  array<string, mixed>  $overrides
     */
    public function handle(Quotation $quotation, array $overrides = []): IncomingPo
    {
        if ($quotation->items()->doesntExist()) {
            throw new InvalidArgumentException(__('documents.errors.quotation_has_no_items'));
        }

        $existing = $quotation->documentLinksAsSource()
            ->with('target')
            ->get()
            ->pluck('target')
            ->first(fn ($target) => $target instanceof IncomingPo && ! $target->status->isCancelled());

        if ($existing !== null) {
            throw new InvalidArgumentException(__('documents.errors.quotation_already_has_incoming_po', [
                'number' => $existing->incoming_po_number,
            ]));
        }

        return DB::transaction(function () use ($quotation, $overrides): IncomingPo {
            $incomingPo = IncomingPo::query()->create(array_merge([
                'customer_id' => $quotation->customer_id,
                'po_date' => now()->toDateString(),
                'expected_delivery_date' => now()->addDays(14)->toDateString(),
                'currency' => $quotation->currency,
                'payment_terms' => $quotation->payment_terms,
                'notes' => $quotation->notes,
                'status' => IncomingPoStatus::Draft,
            ], $overrides));

            $quotation->items()->get()->each(function ($item) use ($incomingPo): void {
                $target = $incomingPo->items()->create(ItemSnapshot::from($item));

                $this->linker->linkItem($item, $target, (float) $item->quantity);
            });

            $incomingPo->recalculateTotals();

            $this->linker->link($quotation, $incomingPo);

            if ($quotation->status === QuotationStatus::Sent
                || $quotation->status === QuotationStatus::Negotiation) {
                $quotation->forceFill(['status' => QuotationStatus::Accepted])->save();
            }

            $quotation->logActivity('converted', 'converted into incoming PO', [
                'incoming_po_id' => $incomingPo->getKey(),
                'incoming_po_number' => $incomingPo->incoming_po_number,
            ]);

            return $incomingPo;
        });
    }
}
