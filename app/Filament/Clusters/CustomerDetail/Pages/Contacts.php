<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Customers\RelationManagers\ContactsRelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class Contacts extends CustomerDetailPage
{
    protected static ?string $slug = 'contacts';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament-panels::pages.page';

    public static function getNavigationLabel(): string
    {
        return __('resources/customer.tabs.contacts');
    }

    public function getTitle(): string
    {
        return __('resources/customer.tabs.contacts');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(ContactsRelationManager::class, [
                'ownerRecord' => $this->customerRecord,
                'pageClass' => static::class,
            ])->key('contacts'),
        ]);
    }
}
