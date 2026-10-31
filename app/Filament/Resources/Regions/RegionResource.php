<?php

namespace App\Filament\Resources\Regions;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\Regions\Schemas\RegionForm;
use App\Filament\Resources\Regions\Schemas\RegionInfolist;
use App\Filament\Resources\Regions\Tables\RegionsTable;
use App\Filament\Support\NavigationGroup;
use App\Models\Region;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

abstract class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = SetupCluster::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Address;

    abstract public static function level(): string;

    public static function form(Schema $schema): Schema
    {
        return RegionForm::configure($schema, static::level());
    }

    public static function infolist(Schema $schema): Schema
    {
        return RegionInfolist::configure($schema, static::level());
    }

    public static function table(Table $table): Table
    {
        return RegionsTable::configure($table, static::level());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('level', static::level());
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->where('level', static::level());
    }
}
