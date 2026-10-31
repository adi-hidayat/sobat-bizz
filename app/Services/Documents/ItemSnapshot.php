<?php

namespace App\Services\Documents;

use App\Models\Item;

final class ItemSnapshot
{
    /**
     * Salin item sebagai snapshot baru — record item tidak pernah dipakai
     * bersama antar dokumen (PRD 5.6).
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function from(Item $item, array $overrides = []): array
    {
        $data = [
            'product_id' => $item->product_id,
            'sku' => $item->sku,
            'name' => $item->name,
            'description' => $item->description,
            'unit' => $item->unit,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'discount_amount' => $item->discount_amount,
            'tax_rate' => $item->tax_rate,
            'sort_order' => $item->sort_order,
        ];

        if ($item->taxes()->exists()) {
            $data['tax_ids'] = $item->tax_ids;
        }

        return array_merge($data, $overrides);
    }
}
