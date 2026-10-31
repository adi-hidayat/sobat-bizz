<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'payment_terms' => '30 hari',
            'currency' => 'IDR',
            'notes' => fake()->optional()->sentence(),
            'status' => InvoiceStatus::Draft,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (): array => ['status' => InvoiceStatus::Sent]);
    }

    public function overdue(): static
    {
        return $this->state(fn (): array => [
            'status' => InvoiceStatus::Overdue,
            'invoice_date' => now()->subDays(45)->toDateString(),
            'due_date' => now()->subDays(15)->toDateString(),
        ]);
    }
}
