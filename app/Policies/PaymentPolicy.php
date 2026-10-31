<?php

namespace App\Policies;

class PaymentPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'payment';
    }
}
