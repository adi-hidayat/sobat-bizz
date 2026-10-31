<?php

namespace App\Models;

use App\Enums\TaxType;
use Database\Factories\TaxFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<TaxFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'is_withholding',
        'is_active',
        'is_default',
        'sort_order',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaxType::class,
            'value' => 'decimal:2',
            'is_withholding' => 'boolean',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function amountFor(float $base): float
    {
        return $this->type->calculate($base, (float) $this->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
