<?php

namespace Database\Factories;

use App\Models\CrmIndustry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmIndustry>
 */
class CrmIndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
