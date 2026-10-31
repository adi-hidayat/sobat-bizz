<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaxType: string implements HasColor, HasLabel
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function getLabel(): string
    {
        return __('enums.tax_type.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Percentage => 'info',
            self::Fixed => 'warning',
        };
    }

    /**
     * Menghitung nilai pajak dari dasar pengenaan.
     */
    public function calculate(float $base, float $value): float
    {
        return match ($this) {
            self::Percentage => round($base * $value / 100, 2),
            self::Fixed => round($value, 2),
        };
    }
}
