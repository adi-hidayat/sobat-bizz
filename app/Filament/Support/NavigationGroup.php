<?php

namespace App\Filament\Support;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case Crm = 'crm';
    case Catalog = 'catalog';
    case Sales = 'sales';
    case Procurement = 'procurement';
    case Settings = 'settings';
    case System = 'system';
    case Address = 'address';

    public function getLabel(): string
    {
        return __('navigation.groups.'.$this->value);
    }
}
