<?php

namespace App\Filament\Resources\CrmJobTitles;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\CrmJobTitles\Pages\ListCrmJobTitles;
use App\Filament\Resources\CrmJobTitles\Schemas\CrmJobTitleForm;
use App\Filament\Resources\CrmJobTitles\Schemas\CrmJobTitleInfolist;
use App\Filament\Resources\CrmJobTitles\Tables\CrmJobTitlesTable;
use App\Filament\Support\NavigationGroup;
use App\Models\CrmJobTitle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CrmJobTitleResource extends Resource
{
    protected static ?string $model = CrmJobTitle::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Crm;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/crm_job_title.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/crm_job_title.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return CrmJobTitleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CrmJobTitleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmJobTitlesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmJobTitles::route('/'),
        ];
    }
}
