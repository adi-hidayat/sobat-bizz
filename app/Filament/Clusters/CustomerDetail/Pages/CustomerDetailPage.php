<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Clusters\CustomerDetailCluster;
use App\Models\Customer;
use Filament\Pages\Page;
use Filament\Panel;

abstract class CustomerDetailPage extends Page
{
    protected static ?string $cluster = CustomerDetailCluster::class;

    protected string $view = 'filament.clusters.customer-detail.pages.documents';

    public Customer $customerRecord;

    public function mount(string $customer): void
    {
        $this->customerRecord = Customer::query()->findOrFail($customer);
    }

    public static function getRoutePath(Panel $panel): string
    {
        return '/{customer}/'.static::getSlug($panel);
    }

    public static function getNavigationUrl(): string
    {
        $customer = request()->route('customer') ?? request()->integer('customer');

        return filled($customer)
            ? static::getUrl(['customer' => $customer])
            : '#';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_customer') ?? false;
    }

    public function getTitle(): string
    {
        return (string) static::getNavigationLabel();
    }
}
