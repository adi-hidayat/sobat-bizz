<?php

namespace Database\Seeders;

use App\Enums\LeadActivityGroup;
use App\Models\LeadActivityType;
use Illuminate\Database\Seeder;

class LeadActivityTypeSeeder extends Seeder
{
    /**
     * Master aktivitas lead bawaan, dikelompokkan berdasarkan grup.
     *
     * @return list<array{key: string, name: string, group: LeadActivityGroup, sort_order: int}>
     */
    public static function defaults(): array
    {
        return [
            ['key' => 'call', 'name' => 'Panggilan Telepon', 'group' => LeadActivityGroup::Communication, 'sort_order' => 1],
            ['key' => 'email', 'name' => 'Email', 'group' => LeadActivityGroup::Communication, 'sort_order' => 2],
            ['key' => 'meeting', 'name' => 'Meeting', 'group' => LeadActivityGroup::Meeting, 'sort_order' => 3],
            ['key' => 'quotation', 'name' => 'Penawaran', 'group' => LeadActivityGroup::Sales, 'sort_order' => 4],
            ['key' => 'task', 'name' => 'Tugas', 'group' => LeadActivityGroup::Task, 'sort_order' => 5],
            ['key' => 'note', 'name' => 'Catatan Internal', 'group' => LeadActivityGroup::Internal, 'sort_order' => 6],
        ];
    }

    public function run(): void
    {
        foreach (static::defaults() as $type) {
            LeadActivityType::query()->updateOrCreate(
                ['key' => $type['key']],
                [
                    'name' => $type['name'],
                    'group' => $type['group'],
                    'is_active' => true,
                    'sort_order' => $type['sort_order'],
                ],
            );
        }
    }
}
