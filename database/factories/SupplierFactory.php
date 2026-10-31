<?php

namespace Database\Factories;

use App\Enums\SupplierStatus;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('021#######'),
            'npwp' => fake()->numerify('##.###.###.#-###.###'),
            'payment_term_days' => 14,
            'status' => SupplierStatus::Active,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => SupplierStatus::Inactive]);
    }
}
