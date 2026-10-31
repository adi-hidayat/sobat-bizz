<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'customer_type' => fake()->randomElement(CustomerType::cases()),
            'npwp' => fake()->numerify('##.###.###.#-###.###'),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('021#######'),
            'payment_term_days' => 30,
            'credit_limit' => fake()->randomElement([0, 50_000_000, 100_000_000]),
            'status' => CustomerStatus::Active,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => CustomerStatus::Inactive]);
    }

    public function fromLead(Lead $lead): static
    {
        return $this->state(fn (): array => ['lead_id' => $lead->getKey()]);
    }
}
