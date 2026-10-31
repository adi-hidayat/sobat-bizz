<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    public function run(): void
    {
        $jobTitles = [
            'System Administrator',
            'Sales Manager',
            'Sales Executive',
            'Purchasing Staff',
            'Warehouse Staff',
            'Finance Staff',
            'Management',
        ];

        foreach ($jobTitles as $index => $name) {
            JobTitle::query()->firstOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
