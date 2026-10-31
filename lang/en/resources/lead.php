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
        'pipeline' => 'Pipeline',
        'table' => 'Table',
        'create' => 'Add Lead',
        'convert' => 'Convert to Customer',
        'convert_description' => 'The lead will be marked as Converted and a new customer will be created from this lead data.',
    ],

    'board' => [
        'title' => 'Lead Pipeline',
        'search_placeholder' => 'Search lead, contact, email, or phone...',
        'clear_search' => 'Clear search',
        'all_sales' => 'All sales',
        'all_sources' => 'All sources',
        'all_ratings' => 'All ratings',
        'all_types' => 'All types',
        'you_option' => ':name (You)',
        'empty_column' => 'No leads yet.',
        'unassigned' => 'Unassigned',
        'move' => 'Move to',
        'lost_reason_title' => 'Lost / unqualified reason',
        'lost_reason_help' => 'Provide a reason before moving the lead to this status.',
        'lost_reason_placeholder' => 'Example: no budget yet, chose a competitor, etc.',
        'errors' => [
            'conversion_blocked' => 'Conversion can only be done through the Convert to Customer action.',
            'lost_reason_required' => 'A reason is required for the Lost or Unqualified status.',
        ],
        'notifications' => [
            'moved' => ':lead moved to :status.',
            'reordered' => ':lead order updated.',
            'not_found' => 'Lead not found.',
            'invalid_status' => 'Invalid status.',
        ],
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
