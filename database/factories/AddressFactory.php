<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'addressable_type' => 'customer',
            'addressable_id' => Customer::factory(),
            'type' => Address::TYPE_BILLING,
            'label' => fake()->optional()->randomElement(['Kantor Pusat', 'Gudang', 'Cabang']),
            'contact_name' => fake()->optional()->name(),
            'phone' => fake()->optional()->numerify('021#######'),
            'address_line' => fake()->streetAddress(),
            'postal_code' => fake()->numerify('#####'),
            'is_primary' => true,
        ];
    }

    public function shipping(): static
    {
        return $this->state(fn (): array => [
            'type' => Address::TYPE_SHIPPING,
            'label' => 'Gudang',
        ]);
    }

    public function office(): static
    {
        return $this->state(fn (): array => [
            'type' => Address::TYPE_OFFICE,
            'label' => 'Kantor',
        ]);
    }
}
