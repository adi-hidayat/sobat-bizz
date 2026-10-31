<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency & Tax
    |--------------------------------------------------------------------------
    |
    | MVP hanya mendukung satu mata uang. Pilihan pajak item dokumen diambil
    | dari master data pajak, dengan pajak default sebagai nilai awal.
    |
    */

    'currency' => env('CRM_CURRENCY', 'IDR'),

    'default_payment_term_days' => (int) env('CRM_DEFAULT_PAYMENT_TERM_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Document Numbering
    |--------------------------------------------------------------------------
    |
    | Setiap jenis dokumen memiliki prefix dan pola nomor tersendiri. Token
    | yang tersedia: {prefix}, {year}, {month}, {day}, {sequence:N}.
    |
    */

    'numbering' => [
        'lead' => ['prefix' => 'LED', 'pattern' => '{prefix}/{year}/{sequence:5}', 'reset' => 'yearly'],
        'customer' => ['prefix' => 'CUS', 'pattern' => '{prefix}/{year}/{sequence:5}', 'reset' => 'yearly'],
        'supplier' => ['prefix' => 'SUP', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'proposal' => ['prefix' => 'PRP', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'quotation' => ['prefix' => 'QTN', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'incoming_po' => ['prefix' => 'IPO', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'purchase_order' => ['prefix' => 'PO', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'delivery_order' => ['prefix' => 'DO', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'invoice' => ['prefix' => 'INV', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
        'payment' => ['prefix' => 'PAY', 'pattern' => '{prefix}/{year}/{sequence:4}', 'reset' => 'yearly'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    */

    'attachments' => [
        'disk' => env('CRM_ATTACHMENT_DISK', 'local'),
        'directory' => 'crm/attachments',
        'max_size_kb' => 10240,
        'accepted_types' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'],
    ],

];
