# Product Requirements Document (PRD)
## CRM & Sales Management System

**Version:** 1.0  
**Status:** MVP  
**Platform:** Web Application

---

## 1. Product Overview

CRM & Sales Management System merupakan aplikasi untuk mengelola keseluruhan proses bisnis penjualan perusahaan, mulai dari pencatatan prospek (Lead), konversi menjadi Customer, penyusunan Proposal dan Quotation, penerimaan Purchase Order dari Customer (Incoming PO), pembuatan Purchase Order internal, Delivery Order, hingga Invoicing.

Aplikasi dirancang menggunakan pendekatan modular dengan document workflow yang saling terhubung.

Setiap dokumen transaksi memiliki header (main table) dan menggunakan satu tabel item terpusat melalui relasi polymorphic (`itemable`).

Dengan pendekatan ini, struktur item dapat digunakan kembali oleh seluruh jenis dokumen tanpa harus membuat tabel item khusus untuk masing-masing dokumen.

## 2. Business Objectives

1. Memusatkan pengelolaan data Lead dan Customer.
2. Mengintegrasikan seluruh proses penjualan dalam satu workflow.
3. Mengurangi duplikasi data transaksi.
4. Mempermudah tracking status setiap dokumen.
5. Menyediakan histori perubahan dan hubungan antar dokumen.
6. Mendukung transaksi produk maupun jasa.
7. Menyediakan informasi pipeline penjualan dan performa bisnis.

## 3. Core Business Workflow

### 3.1 End-to-End Workflow

Lead → Customer → Proposal → Quotation → Incoming PO → Purchase Order → Delivery Order → Invoice

### 3.2 Workflow Description

| Tahapan | Deskripsi |
|---|---|
| Lead | Mencatat prospek atau calon pelanggan. |
| Customer | Lead yang telah dikonversi menjadi pelanggan. |
| Proposal | Penawaran solusi atau ruang lingkup pekerjaan kepada Customer. |
| Quotation | Dokumen penawaran harga resmi. |
| Incoming PO | Purchase Order yang diterbitkan Customer kepada perusahaan. |
| Purchase Order | Purchase Order internal untuk pengadaan barang atau jasa dari Supplier jika diperlukan. |
| Delivery Order | Dokumen pengiriman barang atau penyelesaian penyerahan jasa. |
| Invoice | Dokumen penagihan kepada Customer. |

**Catatan bisnis:** Incoming PO merupakan dokumen yang diterima dari Customer. Purchase Order merupakan dokumen yang diterbitkan perusahaan kepada Supplier. Keduanya memiliki fungsi berbeda dan tidak boleh dianggap sebagai dokumen yang sama.

Untuk transaksi jasa yang tidak memerlukan pengadaan dari Supplier, Purchase Order internal dapat dilewati.

## 4. Functional Requirements

### 4.1 Lead Management

Mengelola calon pelanggan dan peluang bisnis.

**Features:**
- Create, Read, Update, Delete Lead.
- Lead source (Website, Referral, Social Media, Advertisement, Other).
- Company name.
- Contact person.
- Email dan nomor telepon.
- Address.
- Industry.
- Lead status.
- Assigned sales person.
- Notes dan activity history.
- Follow-up date.
- Convert Lead to Customer.

**Lead Status:**
- New
- Contacted
- Qualified
- Unqualified
- Converted
- Lost

**Business Rules:**
- Lead dapat memiliki beberapa kontak.
- Lead yang dikonversi akan menghasilkan Customer.
- Data Lead tidak dihapus setelah konversi.
- Histori aktivitas tetap tersimpan.

### 4.2 Customer Management

Mengelola informasi pelanggan yang sudah terdaftar.

**Features:**
- Customer Code (auto-generated).
- Company Name.
- Customer Type.
- Tax Identification Number (NPWP).
- Billing Address.
- Shipping Address.
- Contact Persons.
- Email dan Phone.
- Payment Terms.
- Credit Limit.
- Customer Status.
- Notes.
- Transaction History.

Customer dapat memiliki beberapa kontak dan alamat.

### 4.3 Proposal Management

Proposal digunakan untuk menyampaikan solusi, ruang lingkup pekerjaan, dan estimasi penawaran kepada Customer.

**Features:**
- Proposal Number.
- Customer.
- Proposal Date.
- Expiry Date.
- Subject.
- Description.
- Scope of Work.
- Terms and Conditions.
- Attachments.
- Items.
- Subtotal, Discount, Tax, Grand Total.
- Status.

**Proposal Status:**
- Draft
- Sent
- Under Review
- Accepted
- Rejected
- Expired
- Cancelled

Proposal dapat dibuat dari Customer dan dapat dikonversi menjadi Quotation.

### 4.4 Quotation Management

Quotation merupakan dokumen penawaran harga resmi yang diberikan kepada Customer.

**Features:**
- Quotation Number.
- Customer.
- Reference Proposal.
- Quotation Date.
- Valid Until.
- Currency.
- Payment Terms.
- Delivery Terms.
- Items.
- Discount.
- Tax.
- Notes.
- Terms and Conditions.
- Attachments.

**Quotation Status:**
- Draft
- Sent
- Negotiation
- Accepted
- Rejected
- Expired
- Cancelled

**Business Rules:**
- Quotation dapat dibuat dari Proposal atau secara langsung.
- Satu Proposal dapat menghasilkan beberapa versi Quotation.
- Quotation yang diterima Customer dapat menjadi dasar Incoming PO.
- Quotation yang sudah dikirim tidak boleh diubah tanpa mekanisme revision.
- Setiap revisi harus memiliki nomor atau versi tersendiri.

### 4.5 Incoming Purchase Order

Incoming PO merupakan dokumen Purchase Order yang diterbitkan oleh Customer sebagai bentuk persetujuan atas penawaran perusahaan.

**Features:**
- Incoming PO Number.
- Customer PO Number.
- Customer.
- Reference Quotation.
- PO Date.
- Expected Delivery Date.
- Currency.
- Payment Terms.
- Items.
- Attachments (PDF atau dokumen PO dari Customer).
- Notes.
- Status.

**Incoming PO Status:**
- Draft
- Received
- Confirmed
- Partially Fulfilled
- Fulfilled
- Cancelled

**Business Rules:**
- Incoming PO harus terhubung dengan Customer.
- Incoming PO dapat dibuat berdasarkan Quotation.
- Item Incoming PO dapat mengambil data dari Quotation.
- Quantity dan harga dapat disesuaikan sesuai dokumen PO yang diterima.
- Satu Incoming PO dapat menghasilkan beberapa Delivery Order.
- Satu Incoming PO dapat menghasilkan beberapa Invoice sesuai skema penagihan.

### 4.6 Purchase Order Management

Purchase Order merupakan dokumen pengadaan internal yang diterbitkan perusahaan kepada Supplier.

**Features:**
- PO Number.
- Supplier.
- Reference Incoming PO (optional).
- Reference Quotation (optional).
- PO Date.
- Expected Delivery Date.
- Currency.
- Payment Terms.
- Items.
- Subtotal.
- Tax.
- Grand Total.
- Notes.
- Attachments.

**PO Status:**
- Draft
- Sent
- Confirmed
- Partially Received
- Completed
- Cancelled

**Business Rules:**
- PO ditujukan kepada Supplier.
- PO dapat dibuat berdasarkan kebutuhan Incoming PO.
- PO dapat dibuat tanpa Incoming PO untuk kebutuhan pengadaan internal.
- Satu Incoming PO dapat memiliki beberapa PO jika pengadaan berasal dari beberapa Supplier.
- PO tidak wajib untuk transaksi jasa yang tidak membutuhkan pengadaan.

### 4.7 Delivery Order Management

Delivery Order digunakan untuk mendokumentasikan pengiriman barang atau penyerahan pekerjaan kepada Customer.

**Features:**
- DO Number.
- Customer.
- Reference Incoming PO.
- Reference Quotation.
- Delivery Date.
- Shipping Address.
- Delivery Method.
- Items.
- Quantity Delivered.
- Received By.
- Delivery Notes.
- Attachment.
- Status.

**DO Status:**
- Draft
- Ready
- Shipped
- Delivered
- Partially Delivered
- Cancelled

**Business Rules:**
- DO dapat dibuat berdasarkan Incoming PO.
- Satu Incoming PO dapat memiliki beberapa DO.
- Quantity yang dikirim tidak boleh melebihi outstanding quantity.
- Sistem harus menghitung quantity yang sudah dikirim dan yang masih tersisa.
- DO dapat menjadi dasar pembuatan Invoice.

Untuk transaksi jasa, Delivery Order dapat digunakan sebagai Service Delivery / Work Completion Report.

### 4.8 Invoice Management

Invoice digunakan untuk menagihkan transaksi kepada Customer.

**Features:**
- Invoice Number.
- Customer.
- Reference Incoming PO.
- Reference DO.
- Reference Quotation.
- Invoice Date.
- Due Date.
- Payment Terms.
- Items.
- Subtotal.
- Discount.
- Tax.
- Grand Total.
- Amount Paid.
- Outstanding Balance.
- Payment History.
- Attachments.

**Invoice Status:**
- Draft
- Sent
- Partially Paid
- Paid
- Overdue
- Cancelled

**Business Rules:**
- Invoice dapat dibuat berdasarkan Incoming PO atau Delivery Order.
- Invoice dapat dibuat secara langsung dengan referensi dokumen sebelumnya.
- Satu Incoming PO dapat memiliki beberapa Invoice.
- Mendukung partial invoicing.
- Mendukung partial payment.
- Outstanding balance dihitung otomatis.
- Invoice yang sudah diterbitkan tidak boleh diubah secara langsung tanpa mekanisme koreksi.

---

## 5. Reusable Itemable Architecture

### 5.1 Architecture Overview

Salah satu requirement utama aplikasi adalah penggunaan satu tabel item terpusat untuk seluruh dokumen transaksi.

Daripada membuat:
- proposal_items
- quotation_items
- incoming_po_items
- purchase_order_items
- delivery_order_items
- invoice_items

Aplikasi menggunakan satu tabel `items` yang berelasi secara polymorphic dengan setiap main document table.

### 5.2 Database Relationship

Setiap dokumen mempunyai tabel utama masing-masing, sedangkan seluruh detail item disimpan pada tabel `items`.

Contoh struktur relasi:

```text
proposals
   |
   | morphMany
   |
   +----------------+
                    |
quotations          |
   |                |
   +---- items -----+
   |                |
incoming_pos        |
   |                |
   +----------------+
   |
purchase_orders
   |
   +----------------+
   |
delivery_orders
   |
   +----------------+
   |
invoices
```

### 5.3 Main Tables

| Table | Fungsi |
|---|---|
| leads | Data prospek |
| customers | Data pelanggan |
| suppliers | Data pemasok |
| proposals | Header proposal |
| quotations | Header quotation |
| incoming_pos | Header incoming PO |
| purchase_orders | Header PO internal |
| delivery_orders | Header delivery order |
| invoices | Header invoice |
| items | Detail item seluruh dokumen |
| products | Master produk |
| services | Master jasa |

### 5.4 Items Table Structure

Contoh struktur tabel:

```sql
CREATE TABLE items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    itemable_type VARCHAR(255) NOT NULL,
    itemable_id BIGINT UNSIGNED NOT NULL,

    product_id BIGINT UNSIGNED NULL,

    description TEXT NOT NULL,
    quantity DECIMAL(15,2) NOT NULL DEFAULT 1,
    unit VARCHAR(50) NULL,

    unit_price DECIMAL(18,2) NOT NULL DEFAULT 0,
    discount DECIMAL(18,2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(8,2) NOT NULL DEFAULT 0,

    subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
    total DECIMAL(18,2) NOT NULL DEFAULT 0,

    sort_order INT DEFAULT 0,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_itemable (itemable_type, itemable_id)
);
```

`itemable_type` menyimpan nama model dokumen, sedangkan `itemable_id` menyimpan ID dari dokumen tersebut.

### 5.5 Laravel Eloquent Implementation

Pada setiap model dokumen:

```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function items(): MorphMany
{
    return $this->morphMany(Item::class, 'itemable');
}
```

Pada model Item:

```php
use Illuminate\Database\Eloquent\Relations\MorphTo;

public function itemable(): MorphTo
{
    return $this->morphTo();
}
```

Contoh penggunaan:

```php
$proposal = Proposal::find(1);

$proposal->items()->create([
    'description' => 'Website Development',
    'quantity' => 1,
    'unit' => 'Project',
    'unit_price' => 15000000,
    'subtotal' => 15000000,
    'total' => 15000000,
]);
```

Penggunaan pada dokumen lain sama:

```php
$quotation->items()->create($itemData);

$incomingPo->items()->create($itemData);

$invoice->items()->create($itemData);
```

### 5.6 Item Reusability Rules

Reusable itemable tidak berarti satu record item yang sama dipakai bersama oleh seluruh dokumen.

Setiap dokumen memiliki record item sendiri agar histori transaksi dan nilai komersialnya tetap independen.

Contoh:

1. Proposal memiliki item Website Development dengan harga Rp15.000.000.
2. Proposal dikonversi menjadi Quotation.
3. Sistem menyalin item Proposal ke Quotation.
4. Customer melakukan negosiasi dan harga berubah menjadi Rp13.500.000.
5. Incoming PO dibuat berdasarkan Quotation yang disetujui.
6. Invoice mengambil item dari Incoming PO atau Delivery Order.

Setiap tahap memiliki snapshot item sendiri.

**Prinsip utama:** Reuse struktur dan data melalui proses copying/mapping, bukan menggunakan satu record item yang sama lintas dokumen.

Hal ini mencegah perubahan pada dokumen berikutnya mengubah histori dokumen sebelumnya.

### 5.7 Important Database Considerations

- Gunakan `DECIMAL`, bukan FLOAT, untuk nilai transaksi.
- Gunakan database transaction ketika melakukan konversi dokumen.
- Tambahkan index pada `itemable_type` dan `itemable_id`.
- Gunakan morph map agar nama model tidak tersimpan langsung dalam database.
- Jangan menghapus item pada dokumen yang sudah diterbitkan.
- Tambahkan snapshot informasi produk seperti SKU, nama produk, unit, dan harga pada item.
- Gunakan soft delete atau status pembatalan sesuai kebutuhan audit.
- Validasi kuantitas antar dokumen melalui relasi source item, bukan hanya berdasarkan deskripsi item.

---

## 6. Document Conversion & Relationship

Setiap konversi dokumen harus menyimpan hubungan dengan dokumen asal.

Contoh relasi:

| Source | Target | Relationship |
|---|---|---|
| Lead | Customer | Conversion |
| Customer | Proposal | One to Many |
| Proposal | Quotation | One to Many |
| Quotation | Incoming PO | One to Many |
| Incoming PO | Purchase Order | One to Many |
| Incoming PO | Delivery Order | One to Many |
| Incoming PO | Invoice | One to Many |
| Delivery Order | Invoice | One to Many |

### 6.1 Document Reference

Setiap dokumen transaksi minimal memiliki:
- ID
- Document Number
- Customer / Supplier ID
- Document Date
- Status
- Created By
- Updated By
- Notes

Dokumen turunan harus memiliki foreign key atau tabel relasi khusus untuk menyimpan referensi sumber.

Untuk mendukung partial conversion dan relasi banyak-ke-banyak, disarankan menggunakan tabel `document_links`.

Contoh:

```text
document_links
--------------
id
source_type
source_id
target_type
target_id
created_at
```

Untuk tracking kuantitas antar dokumen, gunakan tabel tambahan `document_item_links`:

```text
document_item_links
-------------------
id
source_item_id
target_item_id
quantity
created_at
```

Dengan pendekatan ini, sistem dapat mengetahui berapa quantity dari suatu Quotation yang telah masuk ke Incoming PO, dikirim melalui DO, dan ditagihkan melalui Invoice.

---

## 7. Additional Modules

### 7.1 Dashboard

Menampilkan ringkasan aktivitas bisnis:

- Total Leads.
- Total Customers.
- Active Proposals.
- Pending Quotations.
- Incoming PO.
- Active Purchase Orders.
- Pending Delivery Orders.
- Total Invoices.
- Outstanding Receivables.
- Sales Pipeline.

### 7.2 Product & Service Management

Master data untuk produk dan jasa.

Fields:
- SKU / Code.
- Name.
- Description.
- Category.
- Unit.
- Default Price.
- Type (Product / Service).
- Status.

Item transaksi tetap menyimpan snapshot informasi produk/jasa sehingga perubahan master data tidak memengaruhi transaksi historis.

### 7.3 Supplier Management

Mengelola data Supplier:
- Supplier Code.
- Company Name.
- Contact Person.
- Email.
- Phone.
- Address.
- NPWP.
- Payment Terms.
- Status.

### 7.4 Activity Log

Mencatat aktivitas:
- Lead Created.
- Lead Converted.
- Proposal Created.
- Quotation Sent.
- Incoming PO Received.
- PO Created.
- Delivery Completed.
- Invoice Issued.
- Payment Received.
- Document Status Changed.

### 7.5 User & Role Management

Role awal:
- Administrator.
- Sales Manager.
- Sales Executive.
- Purchasing.
- Warehouse.
- Finance.
- Management.

Setiap role memiliki permission sesuai tanggung jawabnya.

---

## 8. Non-Functional Requirements

### Security
- Authentication.
- Role-Based Access Control.
- Permission-based authorization.
- Audit log.
- Secure file upload.
- Protection against unauthorized document access.

### Performance
- Pagination untuk seluruh listing.
- Eager loading untuk relasi.
- Indexing pada foreign keys dan polymorphic relationships.
- Queue untuk email dan proses background.
- Database transaction untuk konversi dokumen.

### Data Integrity
- Document number harus unik.
- Setiap transaksi harus memiliki customer atau supplier yang valid.
- Dokumen yang sudah diterbitkan tidak dapat dihapus secara permanen.
- Perubahan dokumen harus tercatat.
- Konversi dokumen tidak boleh menghasilkan duplikasi transaksi secara tidak sengaja.

---

## 9. MVP Scope & Priorities

### Phase 1 — Core CRM

- [ ] Authentication & User Management.
- [ ] Lead Management.
- [ ] Customer Management.
- [ ] Supplier Management.
- [ ] Product & Service Management.
- [ ] Activity Log.

### Phase 2 — Sales Documents

- [ ] Proposal Management.
- [ ] Quotation Management.
- [ ] Document Conversion.
- [ ] Reusable Itemable Architecture.
- [ ] PDF Document Generation.
- [ ] Email Document Delivery.

### Phase 3 — Order Management

- [ ] Incoming PO Management.
- [ ] Purchase Order Management.
- [ ] Supplier Management Integration.
- [ ] Document Relationship Tracking.

### Phase 4 — Fulfillment & Billing

- [ ] Delivery Order.
- [ ] Partial Delivery.
- [ ] Invoice Management.
- [ ] Partial Invoicing.
- [ ] Payment Recording.
- [ ] Outstanding Receivables.

### Phase 5 — Reporting

- [ ] Sales Pipeline Dashboard.
- [ ] Customer Reports.
- [ ] Sales Performance.
- [ ] Invoice Reports.
- [ ] Outstanding Receivables Reports.

---

## 10. MVP Acceptance Criteria

Sistem dinyatakan memenuhi kebutuhan MVP apabila:

1. User dapat membuat Lead dan mengonversinya menjadi Customer.
2. User dapat membuat Proposal dengan beberapa item.
3. Proposal dapat dikonversi menjadi Quotation tanpa kehilangan detail item.
4. Quotation dapat dikonversi menjadi Incoming PO.
5. Incoming PO dapat menghasilkan Purchase Order internal.
6. Incoming PO dapat menghasilkan beberapa Delivery Order.
7. Delivery Order dapat menghasilkan Invoice.
8. Sistem dapat mengelola partial delivery dan partial invoicing.
9. Semua dokumen menggunakan satu tabel `items` melalui polymorphic relationship.
10. Setiap konversi dokumen menghasilkan snapshot item tersendiri.
11. Sistem dapat menampilkan histori dokumen dari Lead hingga Invoice.
12. Sistem dapat menghitung subtotal, pajak, diskon, grand total, pembayaran, dan outstanding balance secara konsisten.

---

## 11. Recommended Technical Architecture

Rekomendasi implementasi awal:

| Layer | Technology |
|---|---|
| Backend | Laravel |
| Admin Panel | Filament |
| Frontend | Livewire / Filament |
| Database | PostgreSQL |
| Authentication | Laravel Authentication |
| Authorization | Laravel Policies / Filament Shield |
| File Storage | Laravel Filesystem |
| Queue | Laravel Queue |
| PDF | Laravel PDF Generator |

Arsitektur disarankan menggunakan modular monolith agar pengembangan MVP lebih sederhana, tetapi setiap domain bisnis tetap dipisahkan berdasarkan tanggung jawabnya.

### Recommended Domain Modules

```text
CRM
├── Leads
├── Customers
└── Activities

CATALOG
├── Products
├── Services
└── Categories

SALES
├── Proposals
├── Quotations
└── Incoming Purchase Orders

PROCUREMENT
├── Suppliers
└── Purchase Orders

FULFILLMENT
└── Delivery Orders

FINANCE
├── Invoices
└── Payments

CORE
├── Document Items
├── Document Links
├── Document Item Links
├── Attachments
├── Numbering
└── Audit Logs
```

---

## 12. Future Enhancements (Out of MVP Scope)

Fitur yang dapat dikembangkan setelah MVP stabil:

- Customer Portal.
- Supplier Portal.
- E-signature.
- Approval Workflow.
- Automated Follow-up.
- WhatsApp Integration.
- AI Lead Scoring.
- AI-assisted Proposal Generation.
- Sales Forecasting.
- Accounting Integration.
- Inventory Management.
- Multi-company / Multi-branch.
- Multi-currency.
- Tax Configuration.

**Product Direction:** CRM ini dikembangkan sebagai B2B Sales Management System yang mengintegrasikan CRM, Sales Order Processing, Procurement, Fulfillment, dan Billing dalam satu aplikasi.

13. Rekomendasi desain database
Ada beberapa keputusan arsitektur yang menurut saya penting sebelum mulai implementasi.
A. Jangan hanya mengandalkan items untuk tracking dokumen
Saya merekomendasikan tiga lapisan berikut:
1. Main document tables

Menyimpan identitas dan informasi utama dokumen.

proposals, quotations, incoming_pos, purchase_orders, delivery_orders, invoices


2. Shared items table

Menyimpan detail item melalui polymorphic relationship.

items


3. Document mapping tables

Menyimpan hubungan antar dokumen dan detail item yang dikonversi.

document_links, document_item_links


Contoh kasus:

Quotation memiliki 10 unit barang. Customer hanya memesan 6 unit melalui Incoming PO.
- Quotation: 10 unit.
- Incoming PO: 6 unit.
- Outstanding PO: 4 unit.
- Delivery Order pertama: 3 unit.
- Delivery Order kedua: 3 unit.
- Invoice: dapat ditagihkan berdasarkan 3 + 3 unit atau sesuai kesepakatan termin.
Dengan document_item_links, sistem bisa melacak kuantitas secara akurat tanpa mengubah histori dokumen asal.


ERD :
ikuti mermaid-diagram.png

C. Satu hal penting: pemisahan Sales dan Procurement
Saya menyarankan agar alurnya tidak dipaksakan menjadi linear sepenuhnya.
Sales flow
1. Lead
2. Customer
3. Proposal
4. Quotation
5. Incoming PO
6. Delivery Order
7. Invoice

Procurement flow (conditional)
1. Incoming PO
2. Purchase Order → Supplier
3. Barang diterima / proses pengadaan
4. Delivery Order → Customer