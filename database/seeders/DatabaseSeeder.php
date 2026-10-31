<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            DepartmentSeeder::class,
            JobTitleSeeder::class,
            AdminUserSeeder::class,
            StaffSeeder::class,
            LeadSourceSeeder::class,
            TaxSeeder::class,
            MasterDataSeeder::class,
            LeadRatingSeeder::class,
            LeadSeeder::class,
            DemoPipelineSeeder::class,
        ]);
    }
}
