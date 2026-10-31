<?php

return [

    'pdf' => [
        'description' => 'Deskripsi',
        'qty' => 'Qty',
        'unit' => 'Satuan',
        'price' => 'Harga',
        'discount' => 'Diskon',
        'tax' => 'Pajak',
        'amount' => 'Jumlah',
        'subtotal' => 'Subtotal',
        'discount_document' => 'Diskon',
        'tax_total' => 'Pajak',
        'grand_total' => 'Grand Total',
        'paid' => 'Sudah dibayar',
        'outstanding' => 'Sisa tagihan',
        'terms' => 'Syarat & Ketentuan',
        'notes' => 'Catatan',
        'prepared_by' => 'Disiapkan oleh',
        'approved_by' => 'Disetujui oleh',
        'footer' => 'dokumen dibuat otomatis oleh sistem.',
        'party' => [
            'customer' => 'Customer',
            'supplier' => 'Supplier',
        ],
        'meta' => [
            'date' => 'Tanggal',
            'valid_until' => 'Berlaku sampai',
            'version' => 'Versi',
            'customer_po' => 'PO customer',
            'expected_delivery' => 'Perkiraan kirim',
            'expected_arrival' => 'Perkiraan datang',
            'method' => 'Metode',
            'received_by' => 'Diterima oleh',
            'due_date' => 'Jatuh tempo',
            'reference' => 'Referensi',
            'status' => 'Status',
        ],
    ],

    'mail' => [
        'greeting' => 'Kepada Yth. Customer,',
        'attachment_before' => 'Dokumen',
        'attachment_after' => 'kami lampirkan dalam format PDF pada email ini.',
        'thanks' => 'Terima kasih atas kepercayaan Anda.',
        'regards' => 'Hormat kami,',
        'subject' => ':type :number',
        'default_message' => 'Terlampir kami kirimkan dokumen berikut untuk Anda tinjau.',
    ],

    'actions' => [
        'convert_to_quotation' => 'Buat Quotation',
        'convert_to_incoming_po' => 'Buat Incoming PO',
        'revise_quotation' => 'Buat Revisi',
        'convert_to_purchase_order' => 'Buat Purchase Order',
        'convert_to_delivery_order' => 'Buat Delivery Order',
        'create_invoice' => 'Buat Invoice',
        'download_pdf' => 'Unduh PDF',
        'send_email' => 'Kirim Email',
        'change_status' => 'Ubah status',
        'record_payment' => 'Catat Pembayaran',
    ],

    'descriptions' => [
        'convert_to_quotation' => 'Item proposal akan disalin sebagai snapshot baru pada quotation.',
        'revise_quotation' => 'Revisi quotation dibuat sebagai dokumen baru dengan nomor dan versi tersendiri.',
    ],

    'fields' => [
        'customer_po_number' => 'Nomor PO customer',
        'po_date' => 'Tanggal PO',
        'expected_delivery_date' => 'Perkiraan pengiriman',
        'supplier' => 'Supplier',
        'expected_arrival' => 'Perkiraan kedatangan',
        'delivery_date' => 'Tanggal pengiriman',
        'delivery_method' => 'Metode pengiriman',
        'invoice_date' => 'Tanggal invoice',
        'due_date' => 'Jatuh tempo',
        'payment_terms' => 'Termin pembayaran',
        'status_new' => 'Status baru',
        'email_to' => 'Email tujuan',
        'subject' => 'Subjek',
        'message' => 'Pesan',
        'amount' => 'Nominal',
        'payment_date' => 'Tanggal pembayaran',
        'method' => 'Metode',
        'reference' => 'Referensi',
        'notes' => 'Catatan',
    ],

    'errors' => [
        'no_remaining_incoming_po_quantity' => 'Tidak ada kuantitas tersisa pada Incoming PO ini.',
        'incoming_po_has_no_items' => 'Incoming PO belum memiliki item untuk dikonversi.',
        'lead_not_convertible' => 'Lead ini tidak dapat dikonversi menjadi customer.',
        'proposal_has_no_items' => 'Proposal belum memiliki item untuk dikonversi.',
        'quotation_has_no_items' => 'Quotation belum memiliki item untuk dikonversi.',
        'quotation_already_has_incoming_po' => 'Quotation ini sudah memiliki Incoming PO (:number). Gunakan Incoming PO tersebut atau batalkan terlebih dahulu.',
        'no_remaining_invoice_quantity' => 'Tidak ada kuantitas tersisa yang dapat ditagihkan dari dokumen ini.',
        'payment_amount_must_be_positive' => 'Nominal pembayaran harus lebih besar dari nol.',
        'payment_exceeds_outstanding' => 'Nominal pembayaran melebihi sisa tagihan.',
        'quotation_has_no_items_to_revise' => 'Quotation belum memiliki item untuk direvisi.',
        'allocation_exceeds_source' => 'Kuantitas ":name" (:quantity) melebihi jumlah pada dokumen sumber (:source).',
        'payment_below_allocations' => 'Nominal pembayaran tidak boleh lebih kecil dari total alokasi.',
        'allocation_exceeds_payment' => 'Total alokasi melebihi nominal pembayaran.',
    ],

    'notifications' => [
        'quotation_created' => [
            'title' => 'Quotation dibuat',
            'body' => 'Nomor :number.',
        ],
        'incoming_po_created' => [
            'title' => 'Incoming PO dibuat',
            'body' => 'Nomor :number.',
        ],
        'quotation_revision_created' => [
            'title' => 'Revisi quotation dibuat',
            'body' => 'Nomor :number (versi :version).',
        ],
        'purchase_order_created' => [
            'title' => 'Purchase Order dibuat',
            'body' => 'Nomor :number.',
        ],
        'delivery_order_created' => [
            'title' => 'Delivery Order dibuat',
            'body' => 'Nomor :number.',
        ],
        'invoice_created' => [
            'title' => 'Invoice dibuat',
            'body' => 'Nomor :number.',
        ],
        'email_scheduled' => [
            'title' => 'Email dijadwalkan',
            'body' => 'Dokumen akan dikirim ke :email.',
        ],
        'status_updated' => [
            'title' => 'Status diperbarui',
            'body' => 'Status baru: :status.',
        ],
        'status_failed' => [
            'title' => 'Status tidak dapat diubah',
        ],
        'payment_recorded' => [
            'title' => 'Pembayaran tercatat',
            'body' => ':number sebesar :amount. Sisa tagihan :outstanding.',
        ],
    ],

];
