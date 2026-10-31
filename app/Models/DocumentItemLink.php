<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentItemLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'source_item_id',
        'target_item_id',
        'quantity',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function sourceItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'source_item_id');
    }

    public function targetItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'target_item_id');
    }
}
