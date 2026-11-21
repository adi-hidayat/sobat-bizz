<?php

namespace App\Filament\Clusters;

use App\Filament\Clusters\LeadDetail\Pages\LeadInformation;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Clusters\Cluster;
use Illuminate\Database\Eloquent\Model;

class LeadDetailCluster extends Cluster
{
    protected static ?string $slug = 'lead-detail';

    public static function getNavigationLabel(): string
    {
        return __('pages.lead_detail.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('pages.lead_detail.title');
    }

    /**
     * Cluster ini hanya dibuka dari halaman lead, bukan menu sidebar.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * Menjaga konteks lead pada URL cluster dan breadcrumb.
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        $parameters['lead'] ??= request()->route('lead') ?? request()->integer('lead') ?: null;

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    /**
     * Membuka submenu pertama (Information) untuk lead yang dipilih.
     */
    public function mount(): void
    {
        $lead = request()->integer('lead');

        if ($lead <= 0) {
            $this->redirect(LeadResource::getUrl('index'));

            return;
        }

        $this->redirect(LeadInformation::getUrl(['lead' => $lead]));
    }
}
