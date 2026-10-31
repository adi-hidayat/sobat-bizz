<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    use HasEnumLabel;

    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';
    case Other = 'other';
}
