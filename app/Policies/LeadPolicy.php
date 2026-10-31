<?php

namespace App\Policies;

class LeadPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'lead';
    }
}
