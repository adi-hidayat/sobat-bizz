<?php

namespace App\Models\Concerns;

use App\Models\DocumentItemLink;
use App\Models\DocumentLink;
use Illuminate\Database\Eloquent\Model;

trait CascadesDocumentDeletion
{
    /**
     * Dokumen hanya boleh dihapus saat masih Draft, sehingga item, tautan,
     * dan lampiran ikut dibersihkan agar tidak meninggalkan data yatim.
     */
    protected static function bootCascadesDocumentDeletion(): void
    {
        static::deleting(function (Model $model): void {
            $itemIds = $model->items()->pluck('id');

            if ($itemIds->isNotEmpty()) {
                DocumentItemLink::query()
                    ->whereIn('source_item_id', $itemIds)
                    ->orWhereIn('target_item_id', $itemIds)
                    ->delete();

                $model->items()->delete();
            }

            DocumentLink::query()
                ->where('source_type', $model->getMorphClass())
                ->where('source_id', $model->getKey())
                ->delete();

            DocumentLink::query()
                ->where('target_type', $model->getMorphClass())
                ->where('target_id', $model->getKey())
                ->delete();

            if (method_exists($model, 'attachments')) {
                $model->attachments()->delete();
            }
        });
    }
}
