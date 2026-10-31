<?php

namespace Database\Seeders;

use App\Enums\TaxType;
use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * @var list<array{name: string, code: string, type: TaxType, value: float, is_withholding: bool, is_default: bool, sort_order: int}>
     */
    private const TAXES = [
        [
            'name' => 'PPN 11%',
            'code' => 'PPN11',
            'type' => TaxType::Percentage,
            'value' => 11,
            'is_withholding' => false,
            'is_default' => true,
            'sort_order' => 10,
        ],
        [
            'name' => 'PPN 0%',
            'code' => 'PPN0',
            'type' => TaxType::Percentage,
            'value' => 0,
            'is_withholding' => false,
            'is_default' => false,
            'sort_order' => 20,
        ],
        [
            'name' => 'PPh 22',
            'code' => 'PPH22',
            'type' => TaxType::Percentage,
            'value' => 1.5,
            'is_withholding' => true,
            'is_default' => false,
            'sort_order' => 30,
        ],
        [
            'name' => 'PPh 23',
            'code' => 'PPH23',
            'type' => TaxType::Percentage,
            'value' => 2,
            'is_withholding' => true,
            'is_default' => false,
            'sort_order' => 40,
        ],
    ];

    public function run(): void
    {
        foreach (self::TAXES as $tax) {
            Tax::query()->firstOrCreate(
                ['code' => $tax['code']],
                [
                    'name' => $tax['name'],
                    'type' => $tax['type'],
                    'value' => $tax['value'],
                    'is_withholding' => $tax['is_withholding'],
                    'is_default' => $tax['is_default'],
                    'sort_order' => $tax['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }
}
