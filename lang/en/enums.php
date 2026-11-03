<?php

return [

    'customer_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'blacklisted' => 'Blacklisted',
    ],

    'customer_type' => [
        'company' => 'Company',
        'individual' => 'Individual',
        'government' => 'Government',
        'other' => 'Other',
    ],

    'delivery_order_status' => [
        'draft' => 'Draft',
        'ready' => 'Ready',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'partially_delivered' => 'Partially Delivered',
        'cancelled' => 'Cancelled',
    ],

    'document_type' => [
        'lead' => 'Lead',
        'customer' => 'Customer',
        'supplier' => 'Supplier',
        'proposal' => 'Proposal',
        'quotation' => 'Quotation',
        'incoming_po' => 'Incoming PO',
        'purchase_order' => 'Purchase Order',
        'delivery_order' => 'Delivery Order',
        'invoice' => 'Invoice',
        'payment' => 'Payment',
    ],

    'incoming_po_status' => [
        'draft' => 'Draft',
        'received' => 'Received',
        'confirmed' => 'Confirmed',
        'partially_fulfilled' => 'Partially Fulfilled',
        'fulfilled' => 'Fulfilled',
        'cancelled' => 'Cancelled',
    ],

    'invoice_status' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'partially_paid' => 'Partially Paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ],

    'lead_status' => [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'converted' => 'Won',
        'lost' => 'Lost',
    ],

    'lead_type' => [
        'company' => 'Company',
        'individual' => 'Individual',
    ],

    'lead_rating' => [
        'hot' => 'Hot',
        'warm' => 'Warm',
        'cold' => 'Cold',
    ],

    'lead_activity_type' => [
        'call' => 'Call',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'note' => 'Note',
        'task' => 'Task',
    ],

    'payment_method' => [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
        'other' => 'Other',
    ],

    'product_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'product_type' => [
        'product' => 'Product',
        'service' => 'Service',
    ],

    'proposal_status' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'under_review' => 'Under Review',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ],

    'purchase_order_status' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'confirmed' => 'Confirmed',
        'partially_received' => 'Partially Received',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'quotation_status' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'negotiation' => 'Negotiation',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ],

    'supplier_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'tax_type' => [
        'percentage' => 'Percentage',
        'fixed' => 'Fixed amount',
    ],

];
