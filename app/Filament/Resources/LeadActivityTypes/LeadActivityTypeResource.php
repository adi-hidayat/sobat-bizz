<?php

namespace App\Filament\Resources\LeadActivityTypes;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\LeadActivityTypes\Pages\ListLeadActivityTypes;
use App\Filament\Resources\LeadActivityTypes\Schemas\LeadActivityTypeForm;
use App\Filament\Resources\LeadActivityTypes\Schemas\LeadActivityTypeInfolist;
use App\Filament\Resources\LeadActivityTypes\Tables\LeadActivityTypesTable;
use App\Filament\Support\NavigationGroup;
use App\Models\LeadActivityType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LeadActivityTypeResource extends Resource
{
    protected static ?string $model = LeadActivityType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Crm;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/lead_activity_type.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/lead_activity_type.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return LeadActivityTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadActivityTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadActivityTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadActivityTypes::route('/'),
        ];
    }
}
