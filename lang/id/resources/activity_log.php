<?php

return [

    'model_label' => 'Aktivitas',

    'plural_model_label' => 'Log Aktivitas',

    'tabs' => [
        'changes' => 'Perubahan',
    ],

    'sections' => [
        'detail' => 'Detail Aktivitas',
    ],

    'fields' => [
        'subject_id' => 'ID',
        'document_id' => 'ID dokumen',
    ],

    'events' => [
        'created' => 'Dibuat',
        'updated' => 'Diperbarui',
        'deleted' => 'Dihapus',
        'restored' => 'Dipulihkan',
        'status_changed' => 'Status diubah',
        'converted' => 'Dikonversi',
        'revised' => 'Direvisi',
        'invoice_created' => 'Invoice dibuat',
        'payment_received' => 'Pembayaran diterima',
        'procurement_created' => 'Purchase Order dibuat',
        'delivery_order_created' => 'Delivery Order dibuat',
    ],

    'subjects' => [
        'user' => 'Pengguna',
        'lead' => 'Lead',
        'customer' => 'Customer',
        'supplier' => 'Supplier',
        'product' => 'Produk',
        'proposal' => 'Proposal',
        'quotation' => 'Quotation',
        'incoming_po' => 'Incoming PO',
        'purchase_order' => 'Purchase Order',
        'delivery_order' => 'Delivery Order',
        'invoice' => 'Invoice',
        'payment' => 'Pembayaran',
        'activity' => 'Aktivitas',
    ],

];
