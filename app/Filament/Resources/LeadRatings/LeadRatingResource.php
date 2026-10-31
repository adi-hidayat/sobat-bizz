<?php

namespace App\Filament\Resources\LeadRatings;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\LeadRatings\Pages\ListLeadRatings;
use App\Filament\Resources\LeadRatings\Schemas\LeadRatingForm;
use App\Filament\Resources\LeadRatings\Schemas\LeadRatingInfolist;
use App\Filament\Resources\LeadRatings\Tables\LeadRatingsTable;
use App\Filament\Support\NavigationGroup;
use App\Models\LeadRating;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LeadRatingResource extends Resource
{
    protected static ?string $model = LeadRating::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Crm;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/lead_rating.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/lead_rating.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return LeadRatingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadRatingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadRatingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadRatings::route('/'),
        ];
    }
}
