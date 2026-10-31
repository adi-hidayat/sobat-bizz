<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;

    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Unqualified = 'unqualified';
    case Converted = 'converted';
    case Lost = 'lost';

    public function isConvertible(): bool
    {
        return in_array($this, [self::New, self::Contacted, self::Qualified], true);
    }
}
