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
        $colors = [
            ['#6b7280', '#ffffff'],
            ['#059669', '#ffffff'],
            ['#0ea5e9', '#ffffff'],
            ['#10b981', '#ffffff'],
            ['#f59e0b', '#111827'],
            ['#ef4444', '#ffffff'],
        ];

        [$color, $textColor] = fake()->randomElement($colors);

        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'color' => $color,
            'text_color' => $textColor,
            'description' => fake()->optional()->sentence(),
        ];
    }
}
