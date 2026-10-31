<?php

namespace App\Services\Documents;

use App\Models\DocumentItemLink;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\Relation;

final class OutstandingQuantityResolver
{
    /**
     * Jumlah kuantitas item sumber yang sudah dipakai oleh dokumen turunan.
     *
     * @param  list<class-string>  $targetModels
     */
    public function consumed(Item $sourceItem, array $targetModels): float
    {
        $aliases = array_map($this->morphAlias(...), $targetModels);

        $targetItemIds = Item::query()
            ->select('id')
            ->whereIn('itemable_type', $aliases);

        return round((float) DocumentItemLink::query()
            ->where('source_item_id', $sourceItem->getKey())
            ->whereIn('target_item_id', $targetItemIds)
            ->sum('quantity'), 2);
    }

    /**
     * Sisa kuantitas item sumber yang masih bisa dialokasikan.
     *
     * @param  list<class-string>  $targetModels
     */
    public function outstanding(Item $sourceItem, array $targetModels): float
    {
        return round(
            max((float) $sourceItem->quantity - $this->consumed($sourceItem, $targetModels), 0),
            2,
        );
    }

    public function morphAlias(string $modelClass): string
    {
        foreach (Relation::morphMap() as $alias => $class) {
            if ($class === $modelClass) {
                return $alias;
            }
        }

        return (new $modelClass)->getMorphClass();
    }
}
