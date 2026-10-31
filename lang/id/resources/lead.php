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
        'pipeline' => 'Pipeline',
        'table' => 'Tabel',
        'create' => 'Tambah Lead',
        'convert' => 'Konversi ke Customer',
        'convert_description' => 'Lead akan ditandai Converted dan customer baru dibuat dari data lead ini.',
    ],

    'board' => [
        'title' => 'Pipeline Lead',
        'search_placeholder' => 'Cari lead, kontak, email, atau telepon...',
        'clear_search' => 'Hapus pencarian',
        'all_sales' => 'Semua sales',
        'all_sources' => 'Semua sumber',
        'all_ratings' => 'Semua rating',
        'all_types' => 'Semua tipe',
        'you_option' => ':name (Anda)',
        'empty_column' => 'Belum ada lead.',
        'unassigned' => 'Belum ada sales',
        'move' => 'Pindahkan',
        'lost_reason_title' => 'Alasan lost / tidak berkualitas',
        'lost_reason_help' => 'Isi alasan sebelum memindahkan lead ke status ini.',
        'lost_reason_placeholder' => 'Contoh: budget belum tersedia, memilih kompetitor, dsb.',
        'errors' => [
            'conversion_blocked' => 'Konversi hanya bisa melalui aksi Konversi ke Customer.',
            'lost_reason_required' => 'Alasan wajib diisi untuk status Hilang atau Tidak Berkualitas.',
        ],
        'notifications' => [
            'moved' => ':lead dipindahkan ke :status.',
            'reordered' => 'Urutan :lead diperbarui.',
            'not_found' => 'Lead tidak ditemukan.',
            'invalid_status' => 'Status tidak valid.',
        ],
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
