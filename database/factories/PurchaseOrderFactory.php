<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'po_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(10)->toDateString(),
            'currency' => 'IDR',
            'payment_terms' => '14 hari',
            'notes' => fake()->optional()->sentence(),
            'status' => PurchaseOrderStatus::Draft,
        ];
    }
}
