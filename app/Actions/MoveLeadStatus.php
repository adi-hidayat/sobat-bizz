<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class MoveLeadStatus
{
    /**
     * Pindahkan lead ke status baru pada pipeline.
     *
     * Konversi customer tidak termasuk di sini; gunakan ConvertLeadToCustomer.
     */
    public function handle(Lead $lead, LeadStatus $status, ?string $lostReason = null): Lead
    {
        if ($lead->status === LeadStatus::Converted || $status === LeadStatus::Converted) {
            throw new InvalidArgumentException(__('resources/lead.board.errors.conversion_blocked'));
        }

        if ($lead->status === $status) {
            return $lead;
        }

        $requiresReason = $status === LeadStatus::Lost;

        if ($requiresReason) {
            $lostReason = is_string($lostReason) ? trim($lostReason) : null;

            if (blank($lostReason)) {
                throw ValidationException::withMessages([
                    'lost_reason' => __('resources/lead.board.errors.lost_reason_required'),
                ]);
            }
        }

        $lead->status = $status;
        $lead->lost_reason = $requiresReason ? $lostReason : null;
        $lead->save();

        return $lead;
    }
}
