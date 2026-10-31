<?php

namespace App\Policies;

class QuotationPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'quotation';
    }
}
