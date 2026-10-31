<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use App\Enums\Concerns\IsDocumentStatus;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseOrderStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;
    use IsDocumentStatus;

    case Draft = 'draft';
    case Sent = 'sent';
    case Confirmed = 'confirmed';
    case PartiallyReceived = 'partially_received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
