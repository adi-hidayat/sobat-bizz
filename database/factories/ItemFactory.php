<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'itemable_type' => 'proposal',
            'itemable_id' => Proposal::factory(),
            'product_id' => null,
            'sku' => strtoupper(fake()->bothify('??-####')),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement(['pcs', 'unit', 'paket']),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => fake()->randomElement([500_000, 1_500_000, 5_000_000]),
            'discount_amount' => 0,
            'tax_rate' => 11,
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }

    public function withoutTax(): static
    {
        return $this->state(fn (): array => ['tax_rate' => 0]);
    }
}
