<?php

namespace App\Support;

final class Format
{
    public static function money(float|int|string|null $value, string $currency = 'IDR'): string
    {
        return trim($currency.' '.number_format((float) $value, 2, ',', '.'));
    }

    public static function quantity(float|int|string|null $value): string
    {
        $quantity = (float) $value;

        return rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
    }
}
