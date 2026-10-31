<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'industry' => fake()->randomElement(['Teknologi', 'Manufaktur', 'Retail', 'Konstruksi', 'Perbankan']),
            'lead_source_id' => LeadSource::factory(),
            'status' => LeadStatus::New,
            'follow_up_date' => fake()->dateTimeBetween('now', '+1 month'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function qualified(): static
    {
        return $this->state(fn (): array => ['status' => LeadStatus::Qualified]);
    }

    public function converted(): static
    {
        return $this->state(fn (): array => [
            'status' => LeadStatus::Converted,
            'converted_at' => now(),
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (): array => ['status' => LeadStatus::Lost]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn (): array => ['assigned_to' => $user->getKey()]);
    }
}
