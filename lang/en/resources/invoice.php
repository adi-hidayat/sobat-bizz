<?php

return [

    'label' => 'Invoice',

    'plural' => 'Invoices',

    'navigation' => 'Invoice',

    'tabs' => [
        'information' => 'Information',
        'items' => 'Items',
        'summary' => 'Summary',
        'documents' => 'Related Documents',
    ],

    'sections' => [
        'information' => 'Invoice Information',
        'items' => 'Invoice Items',
        'value' => 'Invoice Value',
        'payment' => 'Payment',
        'related_documents' => 'Related Documents',
    ],

    'fields' => [
        'invoice_number' => 'Invoice number',
        'payment_terms_short' => 'Terms',
        'amount_paid' => 'Paid',
        'outstanding_balance' => 'Outstanding balance',
        'source_documents' => 'Source documents',
    ],

    'relation_managers' => [
        'payments' => [
            'title' => 'Payment History',
            'payment_number' => 'Payment number',
            'allocated' => 'Allocated',
        ],
    ],

];
