<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@crm.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'job_title' => 'System Administrator',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->hasRole('Administrator')) {
            $admin->assignRole('Administrator');
        }
    }
}
