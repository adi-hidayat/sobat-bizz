<?php

namespace App\Policies;

class CrmJobTitlePolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'crm_job_title';
    }
}
