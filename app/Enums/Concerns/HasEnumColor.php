<?php

namespace App\Enums\Concerns;

trait HasEnumColor
{
    /**
     * Warna badge yang dipakai konsisten oleh seluruh status dokumen di Filament.
     */
    public function getColor(): string|array|null
    {
        return match ($this->name) {
            'Draft' => 'gray',
            'New', 'Sent', 'Contacted', 'Received', 'Ready', 'Shipped' => 'info',
            'Qualified', 'Confirmed', 'Accepted', 'Active', 'Delivered', 'Completed', 'Fulfilled', 'Paid', 'Converted' => 'success',
            'UnderReview', 'Negotiation', 'PartiallyPaid', 'PartiallyDelivered', 'PartiallyFulfilled', 'PartiallyReceived' => 'warning',
            'Overdue', 'Lost', 'Rejected', 'Unqualified', 'Inactive', 'Blacklisted' => 'danger',
            'Cancelled', 'Expired' => 'danger',
            default => 'gray',
        };
    }
}
