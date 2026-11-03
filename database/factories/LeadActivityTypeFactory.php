<?php

namespace Database\Factories;

use App\Enums\LeadActivityGroup;
use App\Models\LeadActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadActivityType>
 */
class LeadActivityTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => fake()->unique()->words(2, true),
            'group' => LeadActivityGroup::Communication,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function group(LeadActivityGroup $group, string $key): static
    {
        return $this->state(fn (): array => [
            'key' => $key,
            'group' => $group,
        ]);
    }
}
