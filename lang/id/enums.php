<?php

return [

    'customer_status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak aktif',
        'blacklisted' => 'Daftar hitam',
    ],

    'customer_type' => [
        'company' => 'Perusahaan',
        'individual' => 'Perorangan',
        'government' => 'Pemerintah',
        'other' => 'Lainnya',
    ],

    'delivery_order_status' => [
        'draft' => 'Draft',
        'ready' => 'Siap',
        'shipped' => 'Dikirim',
        'delivered' => 'Diterima',
        'partially_delivered' => 'Diterima sebagian',
        'cancelled' => 'Dibatalkan',
    ],

    'document_type' => [
        'lead' => 'Lead',
        'customer' => 'Customer',
        'supplier' => 'Supplier',
        'proposal' => 'Proposal',
        'quotation' => 'Quotation',
        'incoming_po' => 'Incoming PO',
        'purchase_order' => 'Purchase Order',
        'delivery_order' => 'Delivery Order',
        'invoice' => 'Invoice',
        'payment' => 'Pembayaran',
    ],

    'incoming_po_status' => [
        'draft' => 'Draft',
        'received' => 'Diterima',
        'confirmed' => 'Dikonfirmasi',
        'partially_fulfilled' => 'Terpenuhi sebagian',
        'fulfilled' => 'Terpenuhi',
        'cancelled' => 'Dibatalkan',
    ],

    'invoice_status' => [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'partially_paid' => 'Dibayar sebagian',
        'paid' => 'Lunas',
        'overdue' => 'Jatuh tempo',
        'cancelled' => 'Dibatalkan',
    ],

    'lead_status' => [
        'new' => 'Baru',
        'contacted' => 'Dihubungi',
        'qualified' => 'Berkualitas',
        'unqualified' => 'Tidak berkualitas',
        'converted' => 'Terkonversi',
        'lost' => 'Hilang',
    ],

    'lead_type' => [
        'company' => 'Perusahaan',
        'individual' => 'Perorangan',
    ],

    'lead_rating' => [
        'hot' => 'Panas',
        'warm' => 'Hangat',
        'cold' => 'Dingin',
    ],

    'lead_activity_type' => [
        'call' => 'Telepon',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'note' => 'Catatan',
        'task' => 'Tugas',
    ],

    'payment_method' => [
        'cash' => 'Tunai',
        'bank_transfer' => 'Transfer bank',
        'cheque' => 'Cek',
        'other' => 'Lainnya',
    ],

    'product_status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak aktif',
    ],

    'product_type' => [
        'product' => 'Produk',
        'service' => 'Jasa',
    ],

    'proposal_status' => [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'under_review' => 'Sedang ditinjau',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
    ],

    'purchase_order_status' => [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'confirmed' => 'Dikonfirmasi',
        'partially_received' => 'Diterima sebagian',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    'quotation_status' => [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'negotiation' => 'Negosiasi',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
    ],

    'supplier_status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak aktif',
    ],

    'tax_type' => [
        'percentage' => 'Persentase',
        'fixed' => 'Nominal tetap',
    ],

];
