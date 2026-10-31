<?php

namespace App\Filament\Clusters;

use App\Filament\Clusters\CustomerDetail\Pages\CustomerInformation;
use App\Filament\Resources\Customers\CustomerResource;
use Filament\Clusters\Cluster;
use Illuminate\Database\Eloquent\Model;

class CustomerDetailCluster extends Cluster
{
    protected static ?string $slug = 'customer-detail';

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('pages.customer_detail.title');
    }

    /**
     * Cluster ini hanya dibuka dari halaman customer, bukan menu sidebar.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * Menjaga konteks customer pada URL cluster dan breadcrumb.
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        $parameters['customer'] ??= request()->route('customer') ?? request()->integer('customer') ?: null;

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    /**
     * Membuka submenu pertama (Proposal) untuk customer yang dipilih.
     */
    public function mount(): void
    {
        $customer = request()->integer('customer');

        if ($customer <= 0) {
            $this->redirect(CustomerResource::getUrl('index'));

            return;
        }

        $this->redirect(CustomerInformation::getUrl(['customer' => $customer]));
    }
}
