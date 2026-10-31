<?php

namespace App\Services\Documents;

use App\Enums\TaxType;
use App\Models\Item;

final class DocumentTotalsCalculator
{
    /**
     * Hitung nilai satu baris item, termasuk rincian beberapa pajak.
     *
     * @param  list<array<string, mixed>>  $taxes
     * @return array{subtotal: float, tax_amount: float, total: float, taxes: list<array<string, mixed>>}
     */
    public function lineTotals(
        float $quantity,
        float $unitPrice,
        float $discountAmount = 0,
        float $taxRate = 0,
        array $taxes = [],
    ): array {
        $subtotal = round(($quantity * $unitPrice) - $discountAmount, 2);
        $subtotal = max($subtotal, 0.0);

        $taxBreakdown = [];

        foreach ($taxes as $tax) {
            $type = $tax['type'] instanceof TaxType ? $tax['type'] : TaxType::from($tax['type']);
            $value = (float) $tax['value'];

            $taxBreakdown[] = array_merge($tax, [
                'type' => $type,
                'value' => $value,
                'amount' => $type->calculate($subtotal, $value),
            ]);
        }

        if ($taxBreakdown === [] && $taxRate > 0) {
            $taxAmount = round($subtotal * $taxRate / 100, 2);

            $taxBreakdown[] = [
                'type' => TaxType::Percentage,
                'value' => $taxRate,
                'amount' => $taxAmount,
            ];
        }

        $taxAmount = round(array_sum(array_column($taxBreakdown, 'amount')), 2);

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => round($subtotal + $taxAmount, 2),
            'taxes' => $taxBreakdown,
        ];
    }

    /**
     * Hitung total header dokumen dari item yang tersimpan.
     *
     * @param  iterable<Item>  $items
     * @return array{subtotal: float, tax_amount: float, grand_total: float}
     */
    public function documentTotals(iterable $items, float $documentDiscount = 0): array
    {
        $subtotal = 0.0;
        $taxAmount = 0.0;

        foreach ($items as $item) {
            $subtotal += (float) ($item->subtotal ?? 0);
            $taxAmount += (float) ($item->tax_amount ?? 0);
        }

        $subtotal = round($subtotal, 2);
        $taxAmount = round($taxAmount, 2);

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'grand_total' => round($subtotal - $documentDiscount + $taxAmount, 2),
        ];
    }
}
