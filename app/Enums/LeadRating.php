<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadRating: string implements HasColor, HasLabel
{
    use HasEnumLabel;

    case Hot = 'hot';
    case Warm = 'warm';
    case Cold = 'cold';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Hot => 'danger',
            self::Warm => 'warning',
            self::Cold => 'info',
        };
    }
}
