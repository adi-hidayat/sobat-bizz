<?php

namespace Database\Factories;

use App\Enums\LeadActivityType;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadActivity>
 */
class LeadActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'type' => LeadActivityType::Call,
            'subject' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'occurred_at' => now(),
            'due_at' => null,
            'completed_at' => null,
        ];
    }

    public function task(): static
    {
        return $this->state(fn (): array => [
            'type' => LeadActivityType::Task,
            'due_at' => now()->addDay(),
        ]);
    }
}
