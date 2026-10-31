<?php

namespace App\Enums\Concerns;

trait IsDocumentStatus
{
    /**
     * Hanya dokumen berstatus Draft yang boleh diubah secara langsung.
     */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Cancelled], true);
    }
}
