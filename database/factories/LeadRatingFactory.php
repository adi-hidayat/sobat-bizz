<?php

namespace Database\Factories;

use App\Models\LeadRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadRating>
 */
class LeadRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'color' => fake()->randomElement(LeadRating::COLORS),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
