<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasLabel;

enum ProductType: string implements HasLabel
{
    use HasEnumLabel;

    case Product = 'product';
    case Service = 'service';
}
