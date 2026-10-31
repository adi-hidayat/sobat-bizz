<?php

namespace App\Filament\Resources\LeadSources;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\LeadSources\Pages\ListLeadSources;
use App\Filament\Resources\LeadSources\Schemas\LeadSourceForm;
use App\Filament\Resources\LeadSources\Schemas\LeadSourceInfolist;
use App\Filament\Resources\LeadSources\Tables\LeadSourcesTable;
use App\Models\LeadSource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeadSourceResource extends Resource
{
    protected static ?string $model = LeadSource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('resources/lead_source.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/lead_source.plural');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = SetupCluster::class;

    public static function form(Schema $schema): Schema
    {
        return LeadSourceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadSourceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadSourcesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadSources::route('/'),
        ];
    }
}
