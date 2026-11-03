<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadActivityType;
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
            'lead_activity_type_id' => LeadActivityType::query()->firstOrCreate(
                ['key' => 'call'],
                [
                    'name' => 'Panggilan Telepon',
                    'group' => 'communication',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            )->getKey(),
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
            'lead_activity_type_id' => LeadActivityType::query()->firstOrCreate(
                ['key' => 'task'],
                [
                    'name' => 'Tugas',
                    'group' => 'task',
                    'is_active' => true,
                    'sort_order' => 5,
                ],
            )->getKey(),
            'due_at' => now()->addDay(),
        ]);
    }
}
