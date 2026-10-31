<?php

namespace App\Policies;

class IncomingPoPolicy extends DocumentPolicy
{
    protected function resource(): string
    {
        return 'incoming_po';
    }
}
