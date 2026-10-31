<?php

namespace App\Actions;

use App\Enums\ProposalStatus;
use App\Enums\QuotationStatus;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Services\Documents\DocumentLinker;
use App\Services\Documents\ItemSnapshot;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConvertProposalToQuotation
{
    public function __construct(private readonly DocumentLinker $linker) {}

    /**
     * @param  array<string, mixed>  $overrides
     */
    public function handle(Proposal $proposal, array $overrides = []): Quotation
    {
        if ($proposal->items()->doesntExist()) {
            throw new InvalidArgumentException(__('documents.errors.proposal_has_no_items'));
        }

        return DB::transaction(function () use ($proposal, $overrides): Quotation {
            $quotation = Quotation::query()->create(array_merge([
                'customer_id' => $proposal->customer_id,
                'version' => 1,
                'quotation_date' => now()->toDateString(),
                'valid_until' => now()->addDays(14)->toDateString(),
                'currency' => $proposal->currency,
                'payment_terms' => $proposal->customer?->payment_term_days.' hari',
                'notes' => $proposal->notes,
                'terms_and_conditions' => $proposal->terms_and_conditions,
                'status' => QuotationStatus::Draft,
            ], $overrides));

            $proposal->items()->get()->each(function ($item) use ($quotation): void {
                $target = $quotation->items()->create(ItemSnapshot::from($item));

                $this->linker->linkItem($item, $target, (float) $item->quantity);
            });

            $quotation->recalculateTotals();

            $this->linker->link($proposal, $quotation);

            if ($proposal->status === ProposalStatus::Draft) {
                $proposal->forceFill(['status' => ProposalStatus::Accepted])->save();
            }

            $proposal->logActivity('converted', 'converted into quotation', [
                'quotation_id' => $quotation->getKey(),
                'quotation_number' => $quotation->quotation_number,
            ]);

            return $quotation;
        });
    }
}
