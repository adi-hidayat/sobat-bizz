<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use App\Models\LeadRating;
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
            'lead_type' => LeadType::Company,
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'job_title' => fake()->jobTitle(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'industry' => fake()->randomElement(['Teknologi', 'Manufaktur', 'Retail', 'Konstruksi', 'Perbankan']),
            'lead_source_id' => LeadSource::factory(),
            'status' => LeadStatus::New,
            'lead_rating_id' => LeadRating::factory(),
            'estimated_value' => fake()->numberBetween(5_000_000, 100_000_000),
            'expected_close_date' => fake()->dateTimeBetween('now', '+2 months'),
            'follow_up_date' => fake()->dateTimeBetween('now', '+1 month'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function individual(): static
    {
        return $this->state(fn (): array => [
            'lead_type' => LeadType::Individual,
            'company_name' => null,
        ]);
    }

    public function qualified(): static
    {
        return $this->state(fn (): array => ['status' => LeadStatus::Qualified]);
    }

    public function hot(): static
    {
        return $this->state(fn (): array => [
            'lead_rating_id' => LeadRating::query()->where('name', 'Hot')->value('id')
                ?? LeadRating::factory()->create([
                    'name' => 'Hot',
                    'color' => 'danger',
                ])->getKey(),
        ]);
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
