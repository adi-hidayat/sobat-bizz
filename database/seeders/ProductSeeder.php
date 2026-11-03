<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private const CATEGORIES = [
        'Perangkat Keras' => 'Perangkat keras dan infrastruktur jaringan.',
        'Perangkat Lunak' => 'Aplikasi dan perangkat lunak pendukung operasional.',
        'Jasa Profesional' => 'Layanan implementasi, pelatihan, dan dukungan teknis.',
        'Lisensi' => 'Lisensi perangkat lunak berlangganan maupun permanen.',
    ];

    public function run(): void
    {
        $categories = collect(self::CATEGORIES)
            ->mapWithKeys(fn (string $description, string $name): array => [
                $name => ProductCategory::query()->firstOrCreate(
                    ['name' => $name],
                    ['description' => $description],
                ),
            ]);

        foreach ($this->products() as $product) {
            Product::query()->firstOrCreate(['sku' => $product['sku']], [
                'name' => $product['name'],
                'type' => $product['type'],
                'unit' => $product['unit'],
                'default_price' => $product['price'],
                'product_category_id' => $categories[$product['category']]->getKey(),
                'status' => 'active',
            ]);
        }
    }

    /**
     * @return array<int, array{sku: string, name: string, type: string, unit: string, price: int, category: string}>
     */
    private function products(): array
    {
        return [
            // Perangkat Keras
            ['sku' => 'HW-0001', 'name' => 'Server Rack 42U', 'type' => 'product', 'unit' => 'unit', 'price' => 18_500_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0002', 'name' => 'Laptop Bisnis 14"', 'type' => 'product', 'unit' => 'unit', 'price' => 14_000_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0003', 'name' => 'Network Switch 24 Port', 'type' => 'product', 'unit' => 'unit', 'price' => 6_750_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0004', 'name' => 'Router Enterprise Dual WAN', 'type' => 'product', 'unit' => 'unit', 'price' => 9_800_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0005', 'name' => 'Wireless Access Point Indoor', 'type' => 'product', 'unit' => 'unit', 'price' => 2_150_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0006', 'name' => 'Firewall UTM Appliance', 'type' => 'product', 'unit' => 'unit', 'price' => 21_000_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0007', 'name' => 'NAS Storage 24TB', 'type' => 'product', 'unit' => 'unit', 'price' => 32_500_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0008', 'name' => 'Server Blade 2 CPU', 'type' => 'product', 'unit' => 'unit', 'price' => 45_000_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0009', 'name' => 'Monitor Profesional 27"', 'type' => 'product', 'unit' => 'pcs', 'price' => 4_250_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0010', 'name' => 'Dokumen Scanner A3', 'type' => 'product', 'unit' => 'unit', 'price' => 11_750_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0011', 'name' => 'Printer Laser Multifungsi', 'type' => 'product', 'unit' => 'unit', 'price' => 8_900_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0012', 'name' => 'UPS Rackmount 3000VA', 'type' => 'product', 'unit' => 'unit', 'price' => 7_450_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0013', 'name' => 'PDU Rack 16 Outlet', 'type' => 'product', 'unit' => 'unit', 'price' => 3_200_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0014', 'name' => 'Kabel UTP Cat6 Box', 'type' => 'product', 'unit' => 'box', 'price' => 1_350_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0015', 'name' => 'Patch Panel 24 Port', 'type' => 'product', 'unit' => 'unit', 'price' => 1_850_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0016', 'name' => 'CCTV IP Dome', 'type' => 'product', 'unit' => 'unit', 'price' => 2_650_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0017', 'name' => 'NVR 16 Channel', 'type' => 'product', 'unit' => 'unit', 'price' => 12_400_000, 'category' => 'Perangkat Keras'],
            ['sku' => 'HW-0018', 'name' => 'Barcode Scanner Wireless', 'type' => 'product', 'unit' => 'unit', 'price' => 3_750_000, 'category' => 'Perangkat Keras'],

            // Perangkat Lunak
            ['sku' => 'SW-0001', 'name' => 'Aplikasi CRM Custom', 'type' => 'service', 'unit' => 'paket', 'price' => 75_000_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0002', 'name' => 'Sistem Payroll', 'type' => 'product', 'unit' => 'lisensi', 'price' => 38_000_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0003', 'name' => 'Aplikasi Akuntansi Cloud', 'type' => 'product', 'unit' => 'lisensi', 'price' => 12_000_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0004', 'name' => 'ERP Modul Inventori', 'type' => 'product', 'unit' => 'lisensi', 'price' => 28_500_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0005', 'name' => 'Dashboard Business Intelligence', 'type' => 'product', 'unit' => 'lisensi', 'price' => 22_000_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0006', 'name' => 'Aplikasi Point of Sale', 'type' => 'product', 'unit' => 'lisensi', 'price' => 8_500_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0007', 'name' => 'Sistem Helpdesk Tiket', 'type' => 'product', 'unit' => 'lisensi', 'price' => 15_750_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0008', 'name' => 'Integrasi API Payment Gateway', 'type' => 'service', 'unit' => 'paket', 'price' => 9_500_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0009', 'name' => 'Aplikasi Manajemen Aset', 'type' => 'product', 'unit' => 'lisensi', 'price' => 18_250_000, 'category' => 'Perangkat Lunak'],
            ['sku' => 'SW-0010', 'name' => 'Portal Karyawan Digital', 'type' => 'product', 'unit' => 'lisensi', 'price' => 16_500_000, 'category' => 'Perangkat Lunak'],

            // Lisensi
            ['sku' => 'LS-0001', 'name' => 'Lisensi Microsoft 365 Business', 'type' => 'product', 'unit' => 'lisensi', 'price' => 2_100_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0002', 'name' => 'Lisensi Antivirus Endpoint', 'type' => 'product', 'unit' => 'lisensi', 'price' => 650_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0003', 'name' => 'Lisensi Adobe Creative Cloud', 'type' => 'product', 'unit' => 'lisensi', 'price' => 9_300_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0004', 'name' => 'Lisensi Windows Server 2022', 'type' => 'product', 'unit' => 'lisensi', 'price' => 12_800_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0005', 'name' => 'Lisensi SQL Server Standard', 'type' => 'product', 'unit' => 'lisensi', 'price' => 14_500_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0006', 'name' => 'Lisensi Backup Cloud Tahunan', 'type' => 'product', 'unit' => 'lisensi', 'price' => 5_400_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0007', 'name' => 'Lisensi Virtualisasi Tahunan', 'type' => 'product', 'unit' => 'lisensi', 'price' => 7_900_000, 'category' => 'Lisensi'],
            ['sku' => 'LS-0008', 'name' => 'Lisensi Monitoring Jaringan', 'type' => 'product', 'unit' => 'lisensi', 'price' => 4_750_000, 'category' => 'Lisensi'],

            // Jasa Profesional
            ['sku' => 'SV-0001', 'name' => 'Implementasi & Migrasi Data', 'type' => 'service', 'unit' => 'paket', 'price' => 25_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0002', 'name' => 'Pelatihan Pengguna', 'type' => 'service', 'unit' => 'hari', 'price' => 4_500_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0003', 'name' => 'Dukungan Teknis Tahunan', 'type' => 'service', 'unit' => 'tahun', 'price' => 18_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0004', 'name' => 'Konsultasi Arsitektur IT', 'type' => 'service', 'unit' => 'hari', 'price' => 6_500_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0005', 'name' => 'Audit Keamanan Sistem', 'type' => 'service', 'unit' => 'paket', 'price' => 15_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0006', 'name' => 'Pengembangan Modul Kustom', 'type' => 'service', 'unit' => 'paket', 'price' => 35_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0007', 'name' => 'Integrasi Sistem Pihak Ketiga', 'type' => 'service', 'unit' => 'paket', 'price' => 20_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0008', 'name' => 'Deployment & Instalasi', 'type' => 'service', 'unit' => 'hari', 'price' => 3_750_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0009', 'name' => 'Maintenance Aplikasi Tahunan', 'type' => 'service', 'unit' => 'tahun', 'price' => 24_000_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0010', 'name' => 'Managed Service Infrastruktur', 'type' => 'service', 'unit' => 'bulan', 'price' => 12_500_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0011', 'name' => 'Pendampingan Go-Live', 'type' => 'service', 'unit' => 'hari', 'price' => 3_200_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0012', 'name' => 'Optimasi Kinerja Database', 'type' => 'service', 'unit' => 'paket', 'price' => 9_800_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0013', 'name' => 'Migrasi Cloud', 'type' => 'service', 'unit' => 'paket', 'price' => 27_500_000, 'category' => 'Jasa Profesional'],
            ['sku' => 'SV-0014', 'name' => 'SLA Dukungan Prioritas', 'type' => 'service', 'unit' => 'tahun', 'price' => 30_000_000, 'category' => 'Jasa Profesional'],
        ];
    }
}
