<?php

namespace App\Filament\Resources\DeliveryOrders;

use App\Filament\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\DeliveryOrders\Pages\CreateDeliveryOrder;
use App\Filament\Resources\DeliveryOrders\Pages\EditDeliveryOrder;
use App\Filament\Resources\DeliveryOrders\Pages\ListDeliveryOrders;
use App\Filament\Resources\DeliveryOrders\Pages\ViewDeliveryOrder;
use App\Filament\Resources\DeliveryOrders\Schemas\DeliveryOrderForm;
use App\Filament\Resources\DeliveryOrders\Schemas\DeliveryOrderInfolist;
use App\Filament\Resources\DeliveryOrders\Tables\DeliveryOrdersTable;
use App\Filament\Support\NavigationGroup;
use App\Models\DeliveryOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeliveryOrderResource extends Resource
{
    protected static ?string $model = DeliveryOrder::class;

    protected static ?string $recordTitleAttribute = 'do_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('resources/delivery_order.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources/delivery_order.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return DeliveryOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeliveryOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryOrdersTable::configure($table);
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
            'index' => ListDeliveryOrders::route('/'),
            'create' => CreateDeliveryOrder::route('/create'),
            'view' => ViewDeliveryOrder::route('/{record}'),
            'edit' => EditDeliveryOrder::route('/{record}/edit'),
        ];
    }
}
