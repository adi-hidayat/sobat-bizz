<?php

namespace App\Policies;

class InvoicePolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'invoice';
    }
}
