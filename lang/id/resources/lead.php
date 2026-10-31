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
        'identity' => 'Identitas Lead',
        'qualification' => 'Kualifikasi & Follow-up',
        'contact_and_address' => 'Kontak & Alamat',
    ],

    'fields' => [
        'lead_number' => 'Nomor lead',
        'lead' => 'Lead',
        'lead_type' => 'Tipe lead',
        'company' => 'Perusahaan',
        'contact_person' => 'Contact person',
        'person_name' => 'Nama lengkap',
        'sales_person' => 'Sales penanggung jawab',
        'source' => 'Sumber',
        'sales' => 'Sales',
        'follow_up' => 'Follow-up',
        'converted_at' => 'Dikonversi pada',
        'primary_contact' => 'Kontak utama',
        'rating' => 'Rating',
        'estimated_value' => 'Nilai peluang',
        'lost_reason' => 'Alasan lost / tidak berkualitas',
        'last_contacted_at' => 'Terakhir dihubungi',
        'activity_type' => 'Tipe aktivitas',
        'activity_subject' => 'Subjek',
        'activity_occurred_at' => 'Waktu',
        'activity_due_at' => 'Jatuh tempo',
        'activity_completed_at' => 'Selesai pada',
    ],

    'filters' => [
        'overdue_follow_up' => 'Follow-up terlambat',
    ],

    'activity' => [
        'pending' => 'Belum selesai',
        'mark_completed' => 'Tandai selesai',
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
        'activities' => [
            'title' => 'Aktivitas',
        ],
    ],

];
