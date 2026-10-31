<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use App\Enums\Concerns\IsDocumentStatus;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IncomingPoStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;
    use IsDocumentStatus;

    case Draft = 'draft';
    case Received = 'received';
    case Confirmed = 'confirmed';
    case PartiallyFulfilled = 'partially_fulfilled';
    case Fulfilled = 'fulfilled';
    case Cancelled = 'cancelled';
}
