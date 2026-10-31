<?php

namespace App\Filament\Resources\IncomingPos;

use App\Filament\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\IncomingPos\Pages\CreateIncomingPo;
use App\Filament\Resources\IncomingPos\Pages\EditIncomingPo;
use App\Filament\Resources\IncomingPos\Pages\ListIncomingPos;
use App\Filament\Resources\IncomingPos\Pages\ViewIncomingPo;
use App\Filament\Resources\IncomingPos\Schemas\IncomingPoForm;
use App\Filament\Resources\IncomingPos\Schemas\IncomingPoInfolist;
use App\Filament\Resources\IncomingPos\Tables\IncomingPosTable;
use App\Filament\Support\NavigationGroup;
use App\Models\IncomingPo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IncomingPoResource extends Resource
{
    protected static ?string $model = IncomingPo::class;

    protected static ?string $recordTitleAttribute = 'incoming_po_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('resources/incoming_po.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/incoming_po.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return IncomingPoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncomingPoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomingPosTable::configure($table);
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
            'index' => ListIncomingPos::route('/'),
            'create' => CreateIncomingPo::route('/create'),
            'view' => ViewIncomingPo::route('/{record}'),
            'edit' => EditIncomingPo::route('/{record}/edit'),
        ];
    }
}
