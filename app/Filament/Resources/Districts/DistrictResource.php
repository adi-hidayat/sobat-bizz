<?php

namespace App\Filament\Resources\Districts;

use App\Filament\Resources\Districts\Pages\ListDistricts;
use App\Filament\Resources\Regions\RegionResource;
use App\Models\Region;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class DistrictResource extends RegionResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('resources/district.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/district.plural_model_label');
    }

    public static function level(): string
    {
        return Region::LEVEL_DISTRICT;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDistricts::route('/'),
        ];
    }
}
