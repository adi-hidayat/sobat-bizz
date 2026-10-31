<?php

namespace Database\Factories;

use App\Enums\TaxType;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tax>
 */
class TaxFactory extends Factory
{
    protected $model = Tax::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Pajak '.fake()->unique()->word(),
            'code' => strtoupper(fake()->unique()->bothify('TAX###??')),
            'type' => TaxType::Percentage,
            'value' => 11,
            'is_withholding' => false,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => fake()->numberBetween(0, 100),
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function fixed(): static
    {
        return $this->state(fn (): array => [
            'type' => TaxType::Fixed,
            'value' => fake()->numberBetween(1_000, 100_000),
        ]);
    }

    public function withholding(): static
    {
        return $this->state(fn (): array => ['is_withholding' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
