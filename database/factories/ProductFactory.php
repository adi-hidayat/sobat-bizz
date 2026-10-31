<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('??-####')),
            'name' => fake()->words(3, true),
            'product_category_id' => ProductCategory::factory(),
            'type' => ProductType::Product,
            'unit' => fake()->randomElement(['pcs', 'unit', 'box']),
            'default_price' => fake()->randomElement([500_000, 1_500_000, 5_000_000, 15_000_000]),
            'description' => fake()->optional()->sentence(),
            'status' => ProductStatus::Active,
        ];
    }

    public function service(): static
    {
        return $this->state(fn (): array => [
            'type' => ProductType::Service,
            'unit' => 'paket',
            'default_price' => 15_000_000,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => ProductStatus::Inactive]);
    }
}
