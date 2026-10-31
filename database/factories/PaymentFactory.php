<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::BankTransfer,
            'amount' => fake()->randomElement([1_000_000, 5_000_000, 10_000_000]),
            'reference' => strtoupper(fake()->bothify('TRX-####')),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
