<?php

namespace App\Filament\Resources\Taxes;

use App\Filament\Clusters\SetupCluster;
use App\Filament\Resources\Taxes\Pages\ListTaxes;
use App\Filament\Resources\Taxes\Schemas\TaxForm;
use App\Filament\Resources\Taxes\Schemas\TaxInfolist;
use App\Filament\Resources\Taxes\Tables\TaxesTable;
use App\Filament\Support\NavigationGroup;
use App\Models\Tax;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Finance;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = SetupCluster::class;

    public static function getModelLabel(): string
    {
        return __('resources/tax.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/tax.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return TaxForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaxInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxes::route('/'),
        ];
    }
}
