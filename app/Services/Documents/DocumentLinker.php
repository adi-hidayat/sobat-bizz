<?php

namespace App\Services\Documents;

use App\Models\DocumentItemLink;
use App\Models\DocumentLink;
use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

final class DocumentLinker
{
    /**
     * Hubungkan dokumen sumber dengan dokumen turunan.
     */
    public function link(Model $source, Model $target): DocumentLink
    {
        return DocumentLink::query()->firstOrCreate([
            'source_type' => $source->getMorphClass(),
            'source_id' => $source->getKey(),
            'target_type' => $target->getMorphClass(),
            'target_id' => $target->getKey(),
        ], [
            'created_at' => now(),
        ]);
    }

    /**
     * Catat pemetaan kuantitas antar item dokumen.
     */
    public function linkItem(Item $sourceItem, Item $targetItem, float $quantity): DocumentItemLink
    {
        $link = DocumentItemLink::query()->firstOrNew([
            'source_item_id' => $sourceItem->getKey(),
            'target_item_id' => $targetItem->getKey(),
        ]);

        $link->quantity = round($quantity, 2);
        $link->created_at ??= now();
        $link->save();

        return $link;
    }

    /**
     * Putuskan seluruh item link milik sebuah dokumen.
     */
    public function unlinkItems(Model $document): void
    {
        $itemIds = $document->items()->pluck('id');

        if ($itemIds->isEmpty()) {
            return;
        }

        DocumentItemLink::query()
            ->whereIn('source_item_id', $itemIds)
            ->orWhereIn('target_item_id', $itemIds)
            ->delete();
    }
}
