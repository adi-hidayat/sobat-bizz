<?php

namespace Database\Seeders;

use App\Models\CrmIndustry;
use Illuminate\Database\Seeder;

class CrmIndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'Teknologi',
            'Manufaktur',
            'Retail',
            'Konstruksi',
            'Perbankan',
            'Logistik',
            'Kesehatan',
            'Pendidikan',
            'Telekomunikasi',
            'Energi',
            'Pertambangan',
            'Agrikultur',
            'Properti',
            'Asuransi',
            'Pemerintahan',
        ];

        foreach ($industries as $index => $name) {
            CrmIndustry::query()->firstOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
