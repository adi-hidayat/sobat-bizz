<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Customers\Schemas\CustomerInfolist;
use Filament\Schemas\Schema;

class CustomerInformation extends CustomerDetailPage
{
    protected static ?string $slug = 'information';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.clusters.customer-detail.pages.information';

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.information');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.information');
    }

    public function infolist(Schema $schema): Schema
    {
        return CustomerInfolist::configure($schema)
            ->record($this->customerRecord);
    }
}
