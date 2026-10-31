<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use App\Enums\Concerns\IsDocumentStatus;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DeliveryOrderStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;
    use IsDocumentStatus;

    case Draft = 'draft';
    case Ready = 'ready';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case PartiallyDelivered = 'partially_delivered';
    case Cancelled = 'cancelled';
}
