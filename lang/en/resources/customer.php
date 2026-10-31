<?php

return [

    'label' => 'Customer',
    'plural' => 'Customers',

    'tabs' => [
        'detail' => 'Detail',
        'addresses' => 'Addresses',
        'contacts' => 'Contacts',
    ],

    'sections' => [
        'detail' => 'Customer Details',
        'contact_person' => 'Contact Persons',
    ],

    'fields' => [
        'customer_code' => 'Customer code',
        'company' => 'Company',
        'customer_type' => 'Customer type',
        'origin_lead' => 'Origin lead',
        'payment_term' => 'Term (days)',
        'outstanding_receivables' => 'Outstanding receivables',
        'full_address' => 'Full address',
        'primary_contact' => 'Primary contact',
        'days_suffix' => ' days',
    ],

    'placeholders' => [
        'empty_addresses' => 'No addresses yet.',
        'empty_contacts' => 'No contacts yet.',
    ],

    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'actions' => [
        'customer_detail' => 'Customer Details',
        'create_proposal' => 'Create Proposal',
    ],

];
