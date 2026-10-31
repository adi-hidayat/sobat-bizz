<?php

namespace App\Filament\Resources\Quotations;

use App\Filament\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Resources\Quotations\Pages\ViewQuotation;
use App\Filament\Resources\Quotations\Schemas\QuotationForm;
use App\Filament\Resources\Quotations\Schemas\QuotationInfolist;
use App\Filament\Resources\Quotations\Tables\QuotationsTable;
use App\Filament\Support\NavigationGroup;
use App\Models\Quotation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $recordTitleAttribute = 'quotation_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('resources/quotation.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/quotation.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'view' => ViewQuotation::route('/{record}'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }
}
