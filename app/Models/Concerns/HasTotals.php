<?php

namespace App\Models\Concerns;

use App\Services\Documents\DocumentTotalsCalculator;
use Illuminate\Database\Eloquent\Model;

trait HasTotals
{
    protected static function bootHasTotals(): void
    {
        static::saved(function (Model $model): void {
            $model->recalculateTotals();
        });
    }

    /**
     * Hitung ulang total dokumen dari item yang tersimpan.
     *
     * @return array<string, float>
     */
    public function recalculateTotals(): array
    {
        $totals = app(DocumentTotalsCalculator::class)->documentTotals(
            $this->items()->get(['subtotal', 'tax_amount']),
            (float) $this->getAttribute('discount_amount'),
        );

        $this->forceFill($totals)->saveQuietly();

        return $totals;
    }
}
