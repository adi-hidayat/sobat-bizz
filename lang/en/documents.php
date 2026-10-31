<?php

return [

    'pdf' => [
        'description' => 'Description',
        'qty' => 'Qty',
        'unit' => 'Unit',
        'price' => 'Price',
        'discount' => 'Discount',
        'tax' => 'Tax',
        'amount' => 'Amount',
        'subtotal' => 'Subtotal',
        'discount_document' => 'Discount',
        'tax_total' => 'Tax',
        'grand_total' => 'Grand Total',
        'paid' => 'Paid',
        'outstanding' => 'Outstanding balance',
        'terms' => 'Terms & Conditions',
        'notes' => 'Notes',
        'prepared_by' => 'Prepared by',
        'approved_by' => 'Approved by',
        'footer' => 'document generated automatically by the system.',
        'party' => [
            'customer' => 'Customer',
            'supplier' => 'Supplier',
        ],
        'meta' => [
            'date' => 'Date',
            'valid_until' => 'Valid until',
            'version' => 'Version',
            'customer_po' => 'Customer PO',
            'expected_delivery' => 'Expected delivery',
            'expected_arrival' => 'Expected arrival',
            'method' => 'Method',
            'received_by' => 'Received by',
            'due_date' => 'Due date',
            'reference' => 'Reference',
            'status' => 'Status',
        ],
    ],

    'mail' => [
        'greeting' => 'Dear Customer,',
        'attachment_before' => 'Document',
        'attachment_after' => 'is attached to this email in PDF format.',
        'thanks' => 'Thank you for your trust.',
        'regards' => 'Best regards,',
        'subject' => ':type :number',
        'default_message' => 'Please find the following document attached for your review.',
    ],

    'actions' => [
        'convert_to_quotation' => 'Create Quotation',
        'convert_to_incoming_po' => 'Create Incoming PO',
        'revise_quotation' => 'Create Revision',
        'convert_to_purchase_order' => 'Create Purchase Order',
        'convert_to_delivery_order' => 'Create Delivery Order',
        'create_invoice' => 'Create Invoice',
        'download_pdf' => 'Download PDF',
        'send_email' => 'Send Email',
        'change_status' => 'Change status',
        'record_payment' => 'Record Payment',
    ],

    'descriptions' => [
        'convert_to_quotation' => 'Proposal items will be copied as new snapshots on the quotation.',
        'revise_quotation' => 'The quotation revision is created as a new document with its own number and version.',
    ],

    'fields' => [
        'customer_po_number' => 'Customer PO number',
        'po_date' => 'PO date',
        'expected_delivery_date' => 'Expected delivery',
        'supplier' => 'Supplier',
        'expected_arrival' => 'Expected arrival',
        'delivery_date' => 'Delivery date',
        'delivery_method' => 'Delivery method',
        'invoice_date' => 'Invoice date',
        'due_date' => 'Due date',
        'payment_terms' => 'Payment terms',
        'status_new' => 'New status',
        'email_to' => 'Recipient email',
        'subject' => 'Subject',
        'message' => 'Message',
        'amount' => 'Amount',
        'payment_date' => 'Payment date',
        'method' => 'Method',
        'reference' => 'Reference',
        'notes' => 'Notes',
    ],

    'errors' => [
        'no_remaining_incoming_po_quantity' => 'There is no remaining quantity on this Incoming PO.',
        'incoming_po_has_no_items' => 'This Incoming PO has no items to convert.',
        'lead_not_convertible' => 'This lead cannot be converted into a customer.',
        'proposal_has_no_items' => 'This proposal has no items to convert.',
        'quotation_has_no_items' => 'This quotation has no items to convert.',
        'quotation_already_has_incoming_po' => 'This quotation already has an Incoming PO (:number). Use that Incoming PO or cancel it first.',
        'no_remaining_invoice_quantity' => 'There is no remaining quantity to invoice from this document.',
        'payment_amount_must_be_positive' => 'The payment amount must be greater than zero.',
        'payment_exceeds_outstanding' => 'The payment amount exceeds the outstanding balance.',
        'quotation_has_no_items_to_revise' => 'This quotation has no items to revise.',
        'allocation_exceeds_source' => 'The quantity of ":name" (:quantity) exceeds the amount on the source document (:source).',
        'payment_below_allocations' => 'The payment amount cannot be less than the total allocations.',
        'allocation_exceeds_payment' => 'The total allocations exceed the payment amount.',
    ],

    'notifications' => [
        'quotation_created' => [
            'title' => 'Quotation created',
            'body' => 'Number :number.',
        ],
        'incoming_po_created' => [
            'title' => 'Incoming PO created',
            'body' => 'Number :number.',
        ],
        'quotation_revision_created' => [
            'title' => 'Quotation revision created',
            'body' => 'Number :number (version :version).',
        ],
        'purchase_order_created' => [
            'title' => 'Purchase Order created',
            'body' => 'Number :number.',
        ],
        'delivery_order_created' => [
            'title' => 'Delivery Order created',
            'body' => 'Number :number.',
        ],
        'invoice_created' => [
            'title' => 'Invoice created',
            'body' => 'Number :number.',
        ],
        'email_scheduled' => [
            'title' => 'Email scheduled',
            'body' => 'The document will be sent to :email.',
        ],
        'status_updated' => [
            'title' => 'Status updated',
            'body' => 'New status: :status.',
        ],
        'status_failed' => [
            'title' => 'Status could not be changed',
        ],
        'payment_recorded' => [
            'title' => 'Payment recorded',
            'body' => ':number for :amount. Outstanding balance :outstanding.',
        ],
    ],

];
