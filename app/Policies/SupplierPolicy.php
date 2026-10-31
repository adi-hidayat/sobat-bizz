<?php

namespace App\Policies;

class SupplierPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'supplier';
    }
}
