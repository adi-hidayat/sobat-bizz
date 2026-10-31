<?php

namespace Database\Seeders;

use App\Models\LeadRating;
use Illuminate\Database\Seeder;

class LeadRatingSeeder extends Seeder
{
    public function run(): void
    {
        $ratings = [
            [
                'name' => '🔥 Hot',
                'legacy_name' => 'Hot',
                'color' => 'danger',
                'description' => 'Prospek dengan minat tinggi dan siap ditindaklanjuti.',
            ],
            [
                'name' => '🌤️ Warm',
                'legacy_name' => 'Warm',
                'color' => 'warning',
                'description' => 'Prospek dengan minat sedang dan masih perlu pendekatan lanjutan.',
            ],
            [
                'name' => '❄️ Cold',
                'legacy_name' => 'Cold',
                'color' => 'info',
                'description' => 'Prospek dengan minat rendah, belum siap membeli.',
            ],
        ];

        foreach ($ratings as $rating) {
            $record = LeadRating::query()
                ->whereIn('name', [$rating['name'], $rating['legacy_name']])
                ->first();

            if ($record) {
                $record->update([
                    'name' => $rating['name'],
                    'color' => $rating['color'],
                    'description' => $rating['description'],
                ]);

                continue;
            }

            LeadRating::query()->create([
                'name' => $rating['name'],
                'color' => $rating['color'],
                'description' => $rating['description'],
            ]);
        }
    }
}
