<?php

namespace Database\Factories;

use App\Enums\IncomingPoStatus;
use App\Models\Customer;
use App\Models\IncomingPo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomingPo>
 */
class IncomingPoFactory extends Factory
{
    protected $model = IncomingPo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_po_number' => strtoupper(fake()->bothify('CPO-####-??')),
            'customer_id' => Customer::factory(),
            'po_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(14)->toDateString(),
            'currency' => 'IDR',
            'payment_terms' => '30 hari',
            'notes' => fake()->optional()->sentence(),
            'status' => IncomingPoStatus::Draft,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (): array => ['status' => IncomingPoStatus::Confirmed]);
    }
}
