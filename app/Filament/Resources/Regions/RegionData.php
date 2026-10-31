<?php

namespace App\Filament\Resources\Regions;

use App\Models\Region;

final class RegionData
{
    /**
     * Menyelaraskan level, induk, negara, dan sumber data sebelum disimpan.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepare(array $data, string $level): array
    {
        $data['level'] = $level;

        if ($level === Region::LEVEL_COUNTRY) {
            $data['parent_id'] = null;
            $data['country_id'] = null;

            return $data;
        }

        if ($level === Region::LEVEL_STATE) {
            $data['parent_id'] = $data['country_id'] ?? null;
            $data['source'] = Region::query()->whereKey($data['parent_id'])->value('source') ?? 'manual';

            return $data;
        }

        $parent = Region::query()->find($data['parent_id'] ?? null);

        $data['country_id'] = $parent?->country_id ?? ($data['country_id'] ?? null);
        $data['source'] = $parent?->source ?? 'manual';

        return $data;
    }
}
