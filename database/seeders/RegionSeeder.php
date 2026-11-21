<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '1G');

        Artisan::call('crm:download-regions');
        $this->command->getOutput()->write(Artisan::output());

        Artisan::call('crm:import-regions');
        $this->command->getOutput()->write(Artisan::output());
    }
}
