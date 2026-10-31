<?php

namespace App\Policies;

class TaxPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'tax';
    }
}
