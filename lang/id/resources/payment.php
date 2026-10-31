<?php

return [

    'label' => 'Pembayaran',

    'plural' => 'Pembayaran',

    'navigation' => 'Pembayaran',

    'sections' => [
        'data' => 'Data Pembayaran',
    ],

    'fields' => [
        'payment_number' => 'Nomor pembayaran',
        'allocated_amount' => 'Teralokasi',
        'unallocated_amount' => 'Belum teralokasi',
    ],

    'relation_managers' => [
        'allocations' => [
            'title' => 'Alokasi ke Invoice',
            'invoice' => 'Invoice',
            'amount' => 'Nominal alokasi',
            'allocation' => 'Alokasi',
            'add' => 'Tambah alokasi',
            'option' => ':number — sisa :outstanding',
        ],
    ],

];
