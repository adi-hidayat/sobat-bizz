<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadActivityGroup: string implements HasColor, HasLabel
{
    use HasEnumLabel;

    case Communication = 'communication';
    case Meeting = 'meeting';
    case Sales = 'sales';
    case Task = 'task';
    case Internal = 'internal';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Communication => 'info',
            self::Meeting => 'primary',
            self::Sales => 'success',
            self::Task => 'warning',
            self::Internal => 'gray',
        };
    }

    /**
     * Aktivitas yang bersentuhan langsung dengan prospek dan memperbarui waktu terakhir dihubungi.
     */
    public function updatesLastContactedAt(): bool
    {
        return in_array($this, [self::Communication, self::Meeting, self::Sales], true);
    }

    /**
     * Grup yang butuh tanggal jatuh tempo dan bisa ditandai selesai.
     */
    public function isTask(): bool
    {
        return $this === self::Task;
    }
}
