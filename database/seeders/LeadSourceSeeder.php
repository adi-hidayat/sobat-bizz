<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * @var list<array{name: string, sort_order: int}>
     */
    private const SOURCES = [
        ['name' => 'Website', 'sort_order' => 10],
        ['name' => 'Instagram', 'sort_order' => 20],
        ['name' => 'Facebook', 'sort_order' => 30],
        ['name' => 'TikTok', 'sort_order' => 40],
        ['name' => 'WhatsApp', 'sort_order' => 50],
        ['name' => 'Social Media', 'sort_order' => 55],
        ['name' => 'Referral', 'sort_order' => 60],
        ['name' => 'Advertisement', 'sort_order' => 65],
        ['name' => 'Marketplace', 'sort_order' => 70],
        ['name' => 'Pameran', 'sort_order' => 80],
        ['name' => 'Iklan', 'sort_order' => 90],
        ['name' => 'Lainnya', 'sort_order' => 100],
    ];

    public function run(): void
    {
        foreach (self::SOURCES as $source) {
            LeadSource::query()->updateOrCreate(
                ['name' => $source['name']],
                ['sort_order' => $source['sort_order']],
            );
        }
    }
}
