<?php

return [

    'label' => 'Payment',

    'plural' => 'Payments',

    'navigation' => 'Payments',

    'sections' => [
        'data' => 'Payment Data',
    ],

    'fields' => [
        'payment_number' => 'Payment number',
        'allocated_amount' => 'Allocated',
        'unallocated_amount' => 'Unallocated',
    ],

    'relation_managers' => [
        'allocations' => [
            'title' => 'Invoice Allocation',
            'invoice' => 'Invoice',
            'amount' => 'Allocation amount',
            'allocation' => 'Allocation',
            'add' => 'Add allocation',
            'option' => ':number — outstanding :outstanding',
        ],
    ],

];
