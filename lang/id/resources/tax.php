<?php

return [

    'label' => 'Pajak',

    'plural' => 'Pajak',

    'sections' => [
        'data' => 'Data Pajak',
    ],

    'fields' => [
        'name' => 'Nama pajak',
        'type' => 'Tipe perhitungan',
        'is_withholding' => 'Pajak potongan',
        'is_default' => 'Pajak default',
        'withholding' => 'Potongan',
    ],

    'helpers' => [
        'is_withholding' => 'Aktifkan untuk pajak yang dipotong dari nilai dasar, seperti PPh.',
        'is_default' => 'Dipakai sebagai pilihan awal saat membuat dokumen baru.',
    ],

    'placeholders' => [
        'name' => 'PPN 11%',
        'code' => 'PPN11',
    ],

    'options' => [
        'inactive' => ':name (nonaktif)',
    ],

    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
    ],

    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ],

    'filters' => [
        'all_statuses' => 'Semua status',
        'all' => 'Semua',
    ],

];
