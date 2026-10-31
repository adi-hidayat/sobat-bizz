<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;

    case Company = 'company';
    case Individual = 'individual';
    case Government = 'government';
    case Other = 'other';
}
