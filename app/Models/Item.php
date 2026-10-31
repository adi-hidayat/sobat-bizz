<?php

namespace App\Models;

use App\Enums\TaxType;
use App\Services\Documents\DocumentTotalsCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemable_type',
        'itemable_id',
        'product_id',
        'tax_ids',
        'sku',
        'name',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total',
        'sort_order',
    ];

    /**
     * Pilihan pajak yang dikirim dari form sebelum item disimpan.
     *
     * @var list<int>|null
     */
    protected ?array $pendingTaxIds = null;

    /**
     * Rincian pajak hasil perhitungan yang akan disimpan ke item_taxes.
     *
     * @var list<array<string, mixed>>|null
     */
    protected ?array $taxBreakdown = null;

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Item $item): void {
            $usesTaxMaster = $item->usesTaxMaster();
            $taxBreakdown = $usesTaxMaster ? $item->taxSnapshots() : [];

            $totals = app(DocumentTotalsCalculator::class)->lineTotals(
                (float) $item->quantity,
                (float) $item->unit_price,
                (float) $item->discount_amount,
                $usesTaxMaster ? 0 : (float) $item->tax_rate,
                $taxBreakdown,
            );

            if ($usesTaxMaster) {
                $item->tax_rate = round(collect($totals['taxes'])
                    ->where('type', TaxType::Percentage)
                    ->sum('value'), 2);
                $item->taxBreakdown = $totals['taxes'];
            }

            $item->forceFill([
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
            ]);
        });

        static::saved(function (Item $item): void {
            if ($item->taxBreakdown !== null) {
                $item->syncTaxSnapshots($item->taxBreakdown);
                $item->taxBreakdown = null;
            }

            $item->recalculateOwnerTotals();
            $item->syncSingleTargetLink();
        });

        static::deleted(fn (Item $item) => $item->recalculateOwnerTotals());
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(ItemTax::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Daftar ID pajak untuk form. Data lama yang hanya punya tax_rate tetap
     * dicocokkan ke master agar tidak hilang saat item diubah.
     *
     * @return list<int>
     */
    public function getTaxIdsAttribute(): array
    {
        if ($this->pendingTaxIds !== null) {
            return $this->pendingTaxIds;
        }

        if (! $this->exists) {
            return [];
        }

        $taxIds = $this->taxes()->pluck('tax_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($taxIds !== [] || (float) $this->tax_rate <= 0) {
            return $taxIds;
        }

        $legacyTaxId = Tax::query()
            ->where('type', TaxType::Percentage)
            ->where('value', $this->tax_rate)
            ->value('id');

        return $legacyTaxId !== null ? [(int) $legacyTaxId] : [];
    }

    /**
     * @param  mixed  $value
     */
    public function setTaxIdsAttribute($value): void
    {
        $this->pendingTaxIds = collect(is_array($value) ? $value : [])
            ->filter(fn ($id): bool => filled($id))
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(DocumentItemLink::class, 'source_item_id');
    }

    public function targetLinks(): HasMany
    {
        return $this->hasMany(DocumentItemLink::class, 'target_item_id');
    }

    /**
     * Isi snapshot produk/jasa ke item tanpa mengikat nilai historis ke master data.
     */
    public function fillFromProduct(Product $product): static
    {
        $this->product_id = $product->getKey();
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->unit = $product->unit;
        $this->unit_price = $product->default_price;

        if (blank($this->description)) {
            $this->description = $product->description;
        }

        return $this;
    }

    public function recalculateOwnerTotals(): void
    {
        $owner = $this->itemable;

        if ($owner !== null && method_exists($owner, 'recalculateTotals')) {
            $owner->recalculateTotals();
        }
    }

    /**
     * Saat kuantitas item turunan diubah pada dokumen yang masih Draft,
     * pemetaan kuantitas ke item sumber ikut disesuaikan.
     */
    public function syncSingleTargetLink(): void
    {
        if ($this->wasRecentlyCreated || $this->targetLinks()->count() !== 1) {
            return;
        }

        $link = $this->targetLinks()->first();

        if ($link === null) {
            return;
        }

        $link->forceFill(['quantity' => $this->quantity])->save();
    }

    private function usesTaxMaster(): bool
    {
        if ($this->pendingTaxIds !== null) {
            return true;
        }

        return $this->exists && $this->taxes()->exists();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function taxSnapshots(): array
    {
        if ($this->pendingTaxIds !== null) {
            if ($this->pendingTaxIds === []) {
                return [];
            }

            $taxes = Tax::query()
                ->whereKey($this->pendingTaxIds)
                ->get()
                ->keyBy(fn (Tax $tax): int => (int) $tax->getKey());

            $snapshots = [];

            foreach ($this->pendingTaxIds as $sortOrder => $taxId) {
                $tax = $taxes->get($taxId);

                if ($tax === null) {
                    continue;
                }

                $snapshots[] = [
                    'tax_id' => $tax->getKey(),
                    'name' => $tax->name,
                    'code' => $tax->code,
                    'type' => $tax->type,
                    'value' => (float) $tax->value,
                    'sort_order' => $sortOrder,
                ];
            }

            return $snapshots;
        }

        return $this->taxes()->get()
            ->values()
            ->map(fn (ItemTax $tax, int $index): array => [
                'tax_id' => $tax->tax_id,
                'name' => $tax->name,
                'code' => $tax->code,
                'type' => $tax->type,
                'value' => (float) $tax->value,
                'sort_order' => $index,
            ])
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $taxBreakdown
     */
    private function syncTaxSnapshots(array $taxBreakdown): void
    {
        $keepIds = [];

        foreach ($taxBreakdown as $tax) {
            $snapshot = $this->taxes()->updateOrCreate(
                ['tax_id' => $tax['tax_id']],
                [
                    'name' => $tax['name'],
                    'code' => $tax['code'],
                    'type' => $tax['type'],
                    'value' => $tax['value'],
                    'amount' => $tax['amount'],
                    'sort_order' => $tax['sort_order'],
                ],
            );

            $keepIds[] = $snapshot->getKey();
        }

        $query = $this->taxes();

        if ($keepIds === []) {
            $query->delete();

            return;
        }

        $query->whereNotIn('id', $keepIds)->delete();
    }
}
