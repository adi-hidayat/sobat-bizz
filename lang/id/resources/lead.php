<?php

return [

    'label' => 'Lead',
    'plural' => 'Lead',

    'tabs' => [
        'information' => 'Informasi',
        'contact_and_address' => 'Kontak & Alamat',
    ],

    'sections' => [
        'information' => 'Informasi Lead',
        'contact_and_address' => 'Kontak & Alamat',
    ],

    'fields' => [
        'lead_number' => 'Nomor lead',
        'company' => 'Perusahaan',
        'contact_person' => 'Contact person',
        'sales_person' => 'Sales penanggung jawab',
        'source' => 'Sumber',
        'sales' => 'Sales',
        'follow_up' => 'Follow-up',
        'converted_at' => 'Dikonversi pada',
        'primary_contact' => 'Kontak utama',
    ],

    'options' => [
        'inactive' => ':name (nonaktif)',
    ],

    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
    ],

    'actions' => [
        'convert' => 'Konversi ke Customer',
        'convert_description' => 'Lead akan ditandai Converted dan customer baru dibuat dari data lead ini.',
    ],

    'notifications' => [
        'converted_title' => 'Lead berhasil dikonversi',
        'converted_body' => 'Customer :customer_code telah dibuat.',
    ],

    'relation_managers' => [
        'contacts' => [
            'title' => 'Kontak',
        ],
    ],

];
