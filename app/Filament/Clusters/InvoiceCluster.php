<?php

namespace App\Filament\Clusters;

use App\Filament\Support\NavigationGroup;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class InvoiceCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function getNavigationLabel(): string
    {
        return __('pages.invoice_cluster.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('pages.invoice_cluster.title');
    }

    protected static ?int $navigationSort = 5;
}
