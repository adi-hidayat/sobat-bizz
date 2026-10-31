<?php

return [

    'label' => 'Invoice',

    'plural' => 'Invoice',

    'navigation' => 'Invoice',

    'tabs' => [
        'information' => 'Informasi',
        'items' => 'Item',
        'summary' => 'Ringkasan',
        'documents' => 'Dokumen Terkait',
    ],

    'sections' => [
        'information' => 'Informasi Invoice',
        'items' => 'Item Invoice',
        'value' => 'Nilai Invoice',
        'payment' => 'Pembayaran',
        'related_documents' => 'Dokumen Terkait',
    ],

    'fields' => [
        'invoice_number' => 'Nomor invoice',
        'payment_terms_short' => 'Termin',
        'amount_paid' => 'Sudah dibayar',
        'outstanding_balance' => 'Sisa tagihan',
        'source_documents' => 'Dokumen asal',
    ],

    'relation_managers' => [
        'payments' => [
            'title' => 'Riwayat Pembayaran',
            'payment_number' => 'Nomor pembayaran',
            'allocated' => 'Dialokasikan',
        ],
    ],

];
