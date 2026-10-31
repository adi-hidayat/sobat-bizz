<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasColor, HasLabel
{
    use HasEnumLabel;

    case Company = 'company';
    case Individual = 'individual';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Company => 'info',
            self::Individual => 'warning',
        };
    }

    public function toCustomerType(): CustomerType
    {
        return match ($this) {
            self::Company => CustomerType::Company,
            self::Individual => CustomerType::Individual,
        };
    }
}
