<?php

namespace App\Policies;

class PurchaseOrderPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'purchase_order';
    }
}
