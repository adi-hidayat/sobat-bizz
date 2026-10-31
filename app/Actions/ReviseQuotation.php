<?php

namespace App\Actions;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ReviseQuotation
{
    public function __construct(private readonly DocumentLinker $linker) {}

    /**
     * Setiap revisi quotation memiliki nomor dan versi tersendiri (PRD 4.4).
     */
    public function handle(Quotation $quotation): Quotation
    {
        if ($quotation->items()->doesntExist()) {
            throw new InvalidArgumentException(__('documents.errors.quotation_has_no_items_to_revise'));
        }

        return DB::transaction(function () use ($quotation): Quotation {
            $revision = Quotation::query()->create([
                'customer_id' => $quotation->customer_id,
                'version' => $quotation->version + 1,
                'quotation_date' => now()->toDateString(),
                'valid_until' => now()->addDays(14)->toDateString(),
                'currency' => $quotation->currency,
                'payment_terms' => $quotation->payment_terms,
                'delivery_terms' => $quotation->delivery_terms,
                'notes' => $quotation->notes,
                'terms_and_conditions' => $quotation->terms_and_conditions,
                'status' => QuotationStatus::Draft,
            ]);

            $quotation->items()->get()->each(function ($item) use ($revision): void {
                $target = $revision->items()->create(ItemSnapshot::from($item));

                $this->linker->linkItem($item, $target, (float) $item->quantity);
            });

            $revision->recalculateTotals();

            $this->linker->link($quotation, $revision);

            $quotation->logActivity('revised', 'revision created', [
                'revision_id' => $revision->getKey(),
                'revision_number' => $revision->quotation_number,
                'version' => $revision->version,
            ]);

            return $revision;
        });
    }
}
