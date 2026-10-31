<?php

namespace App\Services\Documents;

use App\Models\DocumentItemLink;
use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

final class ValidateAllocationQuantities
{
    /**
     * Pastikan total kuantitas item turunan tidak melebihi kuantitas item sumber.
     */
    public function assertWithinSources(Model $document): void
    {
        $morphClass = $document->getMorphClass();
        $cancelledIds = $document->newQuery()
            ->where('status', 'cancelled')
            ->select('id');

        foreach ($document->items()->get() as $item) {
            foreach ($item->targetLinks()->with('sourceItem')->get() as $link) {
                $sourceItem = $link->sourceItem;

                if ($sourceItem === null) {
                    continue;
                }

                $allocated = (float) DocumentItemLink::query()
                    ->where('source_item_id', $sourceItem->getKey())
                    ->whereIn('target_item_id', function ($query) use ($morphClass, $cancelledIds): void {
                        $query->select('id')
                            ->from('items')
                            ->where('itemable_type', $morphClass)
                            ->whereNotIn('itemable_id', $cancelledIds);
                    })
                    ->sum('quantity');

                $sourceQuantity = (float) $sourceItem->quantity;

                if ($allocated > $sourceQuantity + 0.0001) {
                    throw ValidationException::withMessages([
                        'items' => __('documents.errors.allocation_exceeds_source', [
                            'name' => $sourceItem->name,
                            'quantity' => number_format($allocated, 2, ',', '.'),
                            'source' => number_format($sourceQuantity, 2, ',', '.'),
                        ]),
                    ]);
                }
            }
        }
    }
}
