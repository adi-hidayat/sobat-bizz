<?php

namespace App\Filament\Resources\States;

use App\Filament\Resources\Regions\RegionResource;
use App\Filament\Resources\States\Pages\ListStates;
use App\Models\Region;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class StateResource extends RegionResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('resources/state.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/state.plural_model_label');
    }

    public static function level(): string
    {
        return Region::LEVEL_STATE;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStates::route('/'),
        ];
    }
}
