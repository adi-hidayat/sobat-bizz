<?php

namespace App\Filament\Resources\JobTitles;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\JobTitles\Pages\ListJobTitles;
use App\Filament\Resources\JobTitles\Schemas\JobTitleForm;
use App\Filament\Resources\JobTitles\Schemas\JobTitleInfolist;
use App\Filament\Resources\JobTitles\Tables\JobTitlesTable;
use App\Filament\Support\NavigationGroup;
use App\Models\JobTitle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JobTitleResource extends Resource
{
    protected static ?string $model = JobTitle::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::HumanResources;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/job_title.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/job_title.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return JobTitleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobTitleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobTitlesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobTitles::route('/'),
        ];
    }
}
