<?php

namespace App\Filament\Resources\Countries;

use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Filament\Resources\Regions\RegionResource;
use App\Models\Region;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class CountryResource extends RegionResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('resources/country.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/country.plural_model_label');
    }

    public static function level(): string
    {
        return Region::LEVEL_COUNTRY;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
        ];
    }
}
