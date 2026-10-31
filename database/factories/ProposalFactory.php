<?php

namespace Database\Factories;

use App\Enums\ProposalStatus;
use App\Models\Customer;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'proposal_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(30)->toDateString(),
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'scope_of_work' => fake()->paragraph(),
            'terms_and_conditions' => 'Pembayaran 30 hari setelah invoice diterbitkan.',
            'status' => ProposalStatus::Draft,
            'currency' => 'IDR',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (): array => ['status' => ProposalStatus::Sent]);
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => ['status' => ProposalStatus::Accepted]);
    }
}
