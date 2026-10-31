<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_number' => 'EMP-'.fake()->unique()->numerify('####'),
            'department_id' => Department::factory(),
            'joined_at' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
        ];
    }
}
