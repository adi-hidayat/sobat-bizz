<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Customers\RelationManagers\AddressesRelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class Addresses extends CustomerDetailPage
{
    protected static ?string $slug = 'addresses';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament-panels::pages.page';

    public static function getNavigationLabel(): string
    {
        return __('resources/customer.tabs.addresses');
    }

    public function getTitle(): string
    {
        return __('resources/customer.tabs.addresses');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(AddressesRelationManager::class, [
                'ownerRecord' => $this->customerRecord,
                'pageClass' => static::class,
            ])->key('addresses'),
        ]);
    }
}
