<?php

namespace App\Policies;

class ProposalPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'proposal';
    }
}
