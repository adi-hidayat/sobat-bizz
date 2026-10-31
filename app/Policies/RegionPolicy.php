<?php

namespace App\Policies;

class RegionPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'region';
    }
}
