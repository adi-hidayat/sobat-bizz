<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumColor;
use App\Enums\Concerns\HasEnumLabel;
use App\Enums\Concerns\IsDocumentStatus;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProposalStatus: string implements HasColor, HasLabel
{
    use HasEnumColor;
    use HasEnumLabel;
    use IsDocumentStatus;

    case Draft = 'draft';
    case Sent = 'sent';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
