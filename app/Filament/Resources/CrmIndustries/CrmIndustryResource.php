<?php

namespace App\Filament\Resources\CrmIndustries;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\CrmIndustries\Pages\ListCrmIndustries;
use App\Filament\Resources\CrmIndustries\Schemas\CrmIndustryForm;
use App\Filament\Resources\CrmIndustries\Schemas\CrmIndustryInfolist;
use App\Filament\Resources\CrmIndustries\Tables\CrmIndustriesTable;
use App\Filament\Support\NavigationGroup;
use App\Models\CrmIndustry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CrmIndustryResource extends Resource
{
    protected static ?string $model = CrmIndustry::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Crm;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/crm_industry.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/crm_industry.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return CrmIndustryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CrmIndustryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmIndustriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmIndustries::route('/'),
        ];
    }
}
