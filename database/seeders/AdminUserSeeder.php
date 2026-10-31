<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $jobTitle = JobTitle::query()->firstOrCreate(['name' => 'System Administrator']);
        $department = Department::query()->firstOrCreate(['name' => 'IT']);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@crm.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'job_title_id' => $jobTitle->getKey(),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->hasRole('Administrator')) {
            $admin->assignRole('Administrator');
        }

        Staff::query()->firstOrCreate(
            ['user_id' => $admin->getKey()],
            [
                'employee_number' => 'ADM-001',
                'department_id' => $department->getKey(),
                'joined_at' => now()->toDateString(),
            ],
        );
    }
}
