<?php

return [

    'label' => 'Lead',
    'plural' => 'Leads',

    'tabs' => [
        'information' => 'Information',
        'contact_and_address' => 'Contact & Address',
    ],

    'sections' => [
        'information' => 'Lead Information',
        'contact_and_address' => 'Contact & Address',
    ],

    'fields' => [
        'lead_number' => 'Lead number',
        'company' => 'Company',
        'contact_person' => 'Contact person',
        'sales_person' => 'Responsible sales person',
        'source' => 'Source',
        'sales' => 'Sales',
        'follow_up' => 'Follow-up',
        'converted_at' => 'Converted at',
        'primary_contact' => 'Primary contact',
    ],

    'options' => [
        'inactive' => ':name (inactive)',
    ],

    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'actions' => [
        'convert' => 'Convert to Customer',
        'convert_description' => 'The lead will be marked as Converted and a new customer will be created from this lead data.',
    ],

    'notifications' => [
        'converted_title' => 'Lead converted successfully',
        'converted_body' => 'Customer :customer_code has been created.',
    ],

    'relation_managers' => [
        'contacts' => [
            'title' => 'Contacts',
        ],
    ],

];
