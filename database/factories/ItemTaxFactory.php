<?php

namespace Database\Factories;

use App\Enums\TaxType;
use App\Models\Item;
use App\Models\ItemTax;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemTax>
 */
class ItemTaxFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'tax_id' => Tax::factory(),
            'name' => 'PPN 11%',
            'code' => 'PPN11',
            'type' => TaxType::Percentage,
            'value' => 11,
            'amount' => 0,
            'sort_order' => 0,
        ];
    }
}
