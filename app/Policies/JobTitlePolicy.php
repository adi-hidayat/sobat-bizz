<?php

namespace App\Policies;

class JobTitlePolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'job_title';
    }
}
