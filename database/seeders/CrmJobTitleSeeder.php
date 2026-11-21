<?php

namespace Database\Seeders;

use App\Models\CrmJobTitle;
use Illuminate\Database\Seeder;

class CrmJobTitleSeeder extends Seeder
{
    public function run(): void
    {
        $jobTitles = [
            'Owner',
            'CEO',
            'Direktur',
            'General Manager',
            'Manager',
            'Procurement Manager',
            'Purchasing Manager',
            'IT Manager',
            'Finance Manager',
            'HR Manager',
            'Sales Manager',
            'Marketing Manager',
            'Operations Manager',
            'Procurement Staff',
            'Purchasing Staff',
            'Finance Staff',
            'IT Staff',
            'Engineer',
            'Konsultan',
            'Notaris',
        ];

        foreach ($jobTitles as $index => $name) {
            CrmJobTitle::query()->firstOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
