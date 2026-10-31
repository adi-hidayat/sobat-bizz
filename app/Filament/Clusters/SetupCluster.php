<?php

namespace App\Filament\Clusters;

use App\Filament\Support\NavigationGroup;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SetupCluster extends Cluster
{
    protected static ?string $slug = 'setup';

    public static function getNavigationLabel(): string
    {
        return __('pages.setup.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('pages.setup.title');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static ?int $navigationSort = 1;
}
