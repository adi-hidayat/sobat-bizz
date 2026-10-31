<?php

return [

    'label' => 'Tax',

    'plural' => 'Taxes',

    'sections' => [
        'data' => 'Tax Data',
    ],

    'fields' => [
        'name' => 'Tax name',
        'type' => 'Calculation type',
        'is_withholding' => 'Withholding tax',
        'is_default' => 'Default tax',
        'withholding' => 'Withholding',
    ],

    'helpers' => [
        'is_withholding' => 'Enable for taxes deducted from the base amount, such as income tax.',
        'is_default' => 'Used as the initial selection when creating a new document.',
    ],

    'placeholders' => [
        'name' => 'VAT 11%',
        'code' => 'VAT11',
    ],

    'options' => [
        'inactive' => ':name (inactive)',
    ],

    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'filters' => [
        'all_statuses' => 'All statuses',
        'all' => 'All',
    ],

];
