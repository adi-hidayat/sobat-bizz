<?php

return [

    'label' => 'Customer',
    'plural' => 'Customer',

    'tabs' => [
        'detail' => 'Detail',
        'addresses' => 'Alamat',
        'contacts' => 'Kontak',
    ],

    'sections' => [
        'detail' => 'Detail Customer',
        'contact_person' => 'Contact Person',
    ],

    'fields' => [
        'customer_code' => 'Kode customer',
        'company' => 'Perusahaan',
        'customer_type' => 'Tipe customer',
        'origin_lead' => 'Lead asal',
        'payment_term' => 'Termin (hari)',
        'outstanding_receivables' => 'Piutang berjalan',
        'full_address' => 'Alamat lengkap',
        'primary_contact' => 'Kontak utama',
        'days_suffix' => ' hari',
    ],

    'placeholders' => [
        'empty_addresses' => 'Belum ada alamat.',
        'empty_contacts' => 'Belum ada kontak.',
    ],

    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
    ],

    'actions' => [
        'customer_detail' => 'Detail Customer',
        'create_proposal' => 'Buat Proposal',
    ],

];
