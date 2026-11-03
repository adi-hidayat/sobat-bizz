<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT Sumber Teknologi', 'email' => 'sales@sumberteknologi.test'],
            ['name' => 'CV Mitra Distribusi', 'email' => 'order@mitradistribusi.test'],
        ];

        foreach ($suppliers as $supplier) {
            $record = Supplier::query()->firstOrCreate(['company_name' => $supplier['name']], [
                'email' => $supplier['email'],
                'phone' => '0215550001',
                'payment_term_days' => 14,
                'status' => 'active',
            ]);

            if ($record->addresses()->doesntExist()) {
                $record->addresses()->create([
                    'type' => Address::TYPE_OFFICE,
                    'label' => 'Kantor Pusat',
                    'address_line' => 'Jl. Sudirman No. 1',
                    'postal_code' => '12190',
                    'is_primary' => true,
                ]);
            }
        }

        $customers = [
            ['name' => 'PT Nusantara Sejahtera', 'email' => 'procurement@nusantara.test'],
            ['name' => 'PT Bumi Digital', 'email' => 'it@bumidigital.test'],
            ['name' => 'Koperasi Karya Mandiri', 'email' => 'admin@karyamandiri.test'],
        ];

        foreach ($customers as $customer) {
            $record = Customer::query()->firstOrCreate(['company_name' => $customer['name']], [
                'customer_type' => 'company',
                'email' => $customer['email'],
                'phone' => '0217770001',
                'payment_term_days' => 30,
                'status' => 'active',
            ]);

            if ($record->addresses()->doesntExist()) {
                $record->addresses()->create([
                    'type' => Address::TYPE_BILLING,
                    'label' => 'Kantor Pusat',
                    'address_line' => 'Jl. Gatot Subroto No. 10',
                    'postal_code' => '12930',
                    'is_primary' => true,
                ]);

                $record->addresses()->create([
                    'type' => Address::TYPE_SHIPPING,
                    'label' => 'Gudang',
                    'address_line' => 'Jl. Raya Bekasi Km. 18',
                    'postal_code' => '17112',
                    'is_primary' => true,
                ]);
            }

            Contact::query()->firstOrCreate(
                ['contactable_type' => $record->getMorphClass(), 'contactable_id' => $record->getKey(), 'name' => 'Bagian Pengadaan'],
                ['position' => 'Procurement', 'email' => $customer['email'], 'phone' => '0217770001', 'is_primary' => true],
            );
        }

        $leadSources = LeadSource::query()->pluck('id', 'name');

        $leads = [
            ['name' => 'PT Cahaya Retail', 'source' => 'Website', 'status' => 'new'],
            ['name' => 'PT Sentosa Logistik', 'source' => 'Referral', 'status' => 'contacted'],
            ['name' => 'PT Global Niaga', 'source' => 'Social Media', 'status' => 'qualified'],
        ];

        foreach ($leads as $lead) {
            Lead::query()->firstOrCreate(['company_name' => $lead['name']], [
                'contact_person' => 'Bagian IT',
                'email' => strtolower(str_replace(' ', '.', $lead['name'])).'@example.test',
                'phone' => '081200000001',
                'address' => 'Jakarta',
                'industry' => 'Retail',
                'lead_source_id' => $leadSources[$lead['source']] ?? null,
                'status' => $lead['status'],
                'follow_up_date' => now()->addDays(3),
            ]);
        }
    }
}
