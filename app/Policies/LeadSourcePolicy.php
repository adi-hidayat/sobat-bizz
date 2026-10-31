<?php

namespace App\Policies;

class LeadSourcePolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'lead_source';
    }
}
