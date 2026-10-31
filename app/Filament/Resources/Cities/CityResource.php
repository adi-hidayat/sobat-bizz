<?php

namespace App\Filament\Resources\Cities;

use App\Filament\Resources\Cities\Pages\ListCities;
use App\Filament\Resources\Regions\RegionResource;
use App\Models\Region;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class CityResource extends RegionResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('resources/city.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/city.plural_model_label');
    }

    public static function level(): string
    {
        return Region::LEVEL_CITY;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
        ];
    }
}
