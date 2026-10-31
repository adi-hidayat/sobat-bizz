<?php

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'version' => 1,
            'quotation_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'currency' => 'IDR',
            'payment_terms' => '30 hari',
            'delivery_terms' => '14 hari kerja',
            'notes' => fake()->optional()->sentence(),
            'terms_and_conditions' => 'Harga belum termasuk ongkos kirim.',
            'status' => QuotationStatus::Draft,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (): array => ['status' => QuotationStatus::Sent]);
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => ['status' => QuotationStatus::Accepted]);
    }
}
