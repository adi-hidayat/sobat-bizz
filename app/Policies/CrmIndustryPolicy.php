<?php

namespace App\Policies;

class CrmIndustryPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'crm_industry';
    }
}
