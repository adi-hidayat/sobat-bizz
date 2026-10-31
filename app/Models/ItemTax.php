<?php

namespace App\Models;

use App\Enums\TaxType;
use App\Support\Format;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'tax_id',
        'name',
        'code',
        'type',
        'value',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaxType::class,
            'value' => 'decimal:2',
            'amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function displayLabel(): string
    {
        $value = $this->type === TaxType::Percentage
            ? Format::quantity($this->value).'%'
            : Format::money($this->value);

        return "{$this->name} ({$value})";
    }
}
