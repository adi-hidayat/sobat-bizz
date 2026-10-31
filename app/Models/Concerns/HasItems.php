<?php

namespace App\Models\Concerns;

use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasItems
{
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'itemable')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
