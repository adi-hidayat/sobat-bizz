<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Sales',
            'Finance',
            'Purchasing',
            'Warehouse',
            'IT',
            'Marketing',
            'Human Resources',
            'Customer Service',
            'Legal',
            'Operations',
        ];

        foreach ($departments as $index => $name) {
            Department::query()->firstOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
