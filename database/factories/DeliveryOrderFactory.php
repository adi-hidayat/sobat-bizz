<?php

namespace Database\Factories;

use App\Enums\DeliveryOrderStatus;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryOrder>
 */
class DeliveryOrderFactory extends Factory
{
    protected $model = DeliveryOrder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'delivery_date' => now()->toDateString(),
            'shipping_address' => fake()->address(),
            'delivery_method' => fake()->randomElement(['Kurir Internal', 'JNE', 'Self Pickup']),
            'received_by' => null,
            'delivery_notes' => fake()->optional()->sentence(),
            'status' => DeliveryOrderStatus::Draft,
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (): array => [
            'status' => DeliveryOrderStatus::Delivered,
            'received_by' => fake()->name(),
        ]);
    }
}
