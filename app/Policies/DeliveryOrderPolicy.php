<?php

namespace App\Policies;

class DeliveryOrderPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'delivery_order';
    }
}
