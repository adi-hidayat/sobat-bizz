<?php

namespace App\Filament\Resources\Villages;

use App\Filament\Resources\Regions\RegionResource;
use App\Filament\Resources\Villages\Pages\ListVillages;
use App\Models\Region;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class VillageResource extends RegionResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('resources/village.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/village.plural_model_label');
    }

    public static function level(): string
    {
        return Region::LEVEL_VILLAGE;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillages::route('/'),
        ];
    }
}
