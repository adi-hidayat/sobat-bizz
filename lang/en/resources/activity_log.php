<?php

return [

    'model_label' => 'Activity',

    'plural_model_label' => 'Activity Logs',

    'tabs' => [
        'changes' => 'Changes',
    ],

    'sections' => [
        'detail' => 'Activity Detail',
    ],

    'fields' => [
        'subject_id' => 'ID',
        'document_id' => 'Document ID',
    ],

    'events' => [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'status_changed' => 'Status changed',
        'converted' => 'Converted',
        'revised' => 'Revised',
        'invoice_created' => 'Invoice created',
        'payment_received' => 'Payment received',
        'procurement_created' => 'Purchase Order created',
        'delivery_order_created' => 'Delivery Order created',
    ],

    'subjects' => [
        'user' => 'User',
        'lead' => 'Lead',
        'customer' => 'Customer',
        'supplier' => 'Supplier',
        'product' => 'Product',
        'proposal' => 'Proposal',
        'quotation' => 'Quotation',
        'incoming_po' => 'Incoming PO',
        'purchase_order' => 'Purchase Order',
        'delivery_order' => 'Delivery Order',
        'invoice' => 'Invoice',
        'payment' => 'Payment',
        'activity' => 'Activity',
    ],

];
