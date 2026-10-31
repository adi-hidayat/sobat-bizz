<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $departmentIds = Department::query()->pluck('id');

        if ($departmentIds->isEmpty()) {
            $departmentIds = collect([Department::factory()->create()->getKey()]);
        }

        $jobTitleIds = JobTitle::query()->pluck('id');

        if ($jobTitleIds->isEmpty()) {
            $jobTitleIds = collect([JobTitle::factory()->create()->getKey()]);
        }

        $roleIds = Role::query()
            ->where('name', '!=', 'Administrator')
            ->pluck('id');

        for ($index = 1; $index <= 25; $index++) {
            $user = User::query()->firstOrCreate(
                ['email' => sprintf('staff%02d@crm.test', $index)],
                [
                    'name' => fake()->name(),
                    'password' => 'password',
                    'phone' => fake()->numerify('08##########'),
                    'job_title_id' => $jobTitleIds->random(),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );

            if ($user->roles()->doesntExist() && $roleIds->isNotEmpty()) {
                $role = Role::query()->find($roleIds->random());

                if ($role !== null) {
                    $user->assignRole($role);
                }
            }

            Staff::query()->firstOrCreate(
                ['user_id' => $user->getKey()],
                [
                    'employee_number' => 'EMP-'.str_pad((string) (1000 + $index), 4, '0', STR_PAD_LEFT),
                    'department_id' => $departmentIds->random(),
                    'joined_at' => fake()->dateTimeBetween('-3 years', 'now'),
                ],
            );
        }
    }
}
