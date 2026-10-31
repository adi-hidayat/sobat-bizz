<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use App\Models\LeadRating;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $sourceIds = LeadSource::query()->pluck('id');

        if ($sourceIds->isEmpty()) {
            $sourceIds = collect([LeadSource::factory()->create()->getKey()]);
        }

        $userIds = User::query()->pluck('id');

        $ratingIds = LeadRating::query()->pluck('id');

        if ($ratingIds->isEmpty()) {
            $ratingIds = collect([LeadRating::factory()->create()->getKey()]);
        }

        $industries = ['Teknologi', 'Manufaktur', 'Retail', 'Konstruksi', 'Perbankan', 'Logistik', 'Kesehatan', 'Pendidikan'];

        foreach (LeadStatus::cases() as $status) {
            $count = fake()->numberBetween(10, 20);

            for ($index = 1; $index <= $count; $index++) {
                $isCompany = fake()->boolean(70);
                $requiresLostReason = in_array($status, [LeadStatus::Lost, LeadStatus::Unqualified], true);

                $lead = Lead::query()->firstOrCreate(
                    ['email' => sprintf('seed-%s-%02d@example.test', $status->value, $index)],
                    [
                        'lead_type' => $isCompany ? LeadType::Company : LeadType::Individual,
                        'company_name' => $isCompany ? fake()->company() : null,
                        'contact_person' => fake()->name(),
                        'job_title' => fake()->jobTitle(),
                        'phone' => fake()->numerify('08##########'),
                        'address' => fake()->address(),
                        'industry' => fake()->randomElement($industries),
                        'lead_source_id' => $sourceIds->random(),
                        'status' => $status,
                        'lead_rating_id' => $ratingIds->random(),
                        'estimated_value' => fake()->numberBetween(5, 150) * 1_000_000,
                        'expected_close_date' => fake()->dateTimeBetween('-1 month', '+3 months'),
                        'assigned_to' => $userIds->isNotEmpty() ? $userIds->random() : null,
                        'follow_up_date' => fake()->dateTimeBetween('-10 days', '+1 month'),
                        'notes' => fake()->optional()->sentence(),
                        'lost_reason' => $requiresLostReason ? fake()->sentence() : null,
                        'converted_at' => $status === LeadStatus::Converted ? now() : null,
                        'last_contacted_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
                    ],
                );

                if ($userIds->count() > 1) {
                    $teamIds = $userIds
                        ->reject(fn (int $userId): bool => $userId === (int) $lead->assigned_to)
                        ->shuffle()
                        ->take(fake()->numberBetween(0, 2))
                        ->all();

                    if ($teamIds !== []) {
                        $lead->salesTeam()->syncWithoutDetaching($teamIds);
                    }
                }
            }
        }
    }
}
