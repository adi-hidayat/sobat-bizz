<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    use HasEnumLabel;

    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Unqualified = 'unqualified';
    case Converted = 'converted';
    case Lost = 'lost';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'primary',
            self::Qualified => 'success',
            self::Unqualified => 'warning',
            self::Converted => 'success',
            self::Lost => 'danger',
        };
    }

    public function isConvertible(): bool
    {
        return in_array($this, [self::New, self::Contacted, self::Qualified], true);
    }
}
