<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CustomerStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;

    case Active = 'active';
    case Inactive = 'inactive';
    case Blacklisted = 'blacklisted';
}
