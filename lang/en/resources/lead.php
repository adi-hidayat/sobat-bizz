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
        'identity' => 'Lead Identity',
        'qualification' => 'Qualification & Follow-up',
        'contact_and_address' => 'Contact & Address',
    ],

    'fields' => [
        'lead_number' => 'Lead number',
        'lead' => 'Lead',
        'lead_type' => 'Lead type',
        'company' => 'Company',
        'contact_person' => 'Contact person',
        'person_name' => 'Full name',
        'sales_person' => 'Responsible sales person',
        'source' => 'Source',
        'sales' => 'Sales',
        'follow_up' => 'Follow-up',
        'converted_at' => 'Converted at',
        'primary_contact' => 'Primary contact',
        'rating' => 'Rating',
        'estimated_value' => 'Estimated value',
        'lost_reason' => 'Lost / unqualified reason',
        'last_contacted_at' => 'Last contacted',
        'activity_type' => 'Activity type',
        'activity_subject' => 'Subject',
        'activity_occurred_at' => 'Date',
        'activity_due_at' => 'Due date',
        'activity_completed_at' => 'Completed at',
    ],

    'filters' => [
        'overdue_follow_up' => 'Overdue follow-up',
    ],

    'activity' => [
        'pending' => 'Pending',
        'mark_completed' => 'Mark as completed',
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
        'activities' => [
            'title' => 'Activities',
        ],
    ],

];
