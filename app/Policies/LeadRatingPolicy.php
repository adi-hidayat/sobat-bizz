<?php

namespace App\Policies;

class LeadRatingPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'lead_rating';
    }
}
