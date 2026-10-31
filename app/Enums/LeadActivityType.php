<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadActivityType: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;

    case Call = 'call';
    case Email = 'email';
    case Meeting = 'meeting';
    case Note = 'note';
    case Task = 'task';

    /**
     * Aktivitas kontak langsung yang memperbarui waktu terakhir dihubungi.
     */
    public function updatesLastContactedAt(): bool
    {
        return in_array($this, [self::Call, self::Email, self::Meeting], true);
    }
}
