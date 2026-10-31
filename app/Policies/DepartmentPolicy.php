<?php

namespace App\Policies;

class DepartmentPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'department';
    }
}
