<?php

namespace Database\Seeders;

use App\Actions\ConvertIncomingPoToDeliveryOrder;
use App\Actions\ConvertIncomingPoToPurchaseOrder;
use App\Actions\ConvertProposalToQuotation;
use App\Actions\ConvertQuotationToIncomingPo;
use App\Actions\CreateInvoiceFromDocument;
use App\Actions\RecordInvoicePayment;
use App\Enums\CustomerStatus;
use App\Enums\DeliveryOrderStatus;
use App\Enums\IncomingPoStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ProposalStatus;
use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Proposal;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DemoPipelineSeeder extends Seeder
{
    public function run(): void
    {
        if (Proposal::query()->exists()) {
            return;
        }

        $customer = Customer::query()
            ->where('status', CustomerStatus::Active->value)
            ->orderBy('id')
            ->first();

        if ($customer === null) {
            return;
        }

        $supplier = Supplier::query()->orderBy('id')->first();
        $products = Product::query()->orderBy('id')->take(3)->get();

        $proposal = Proposal::query()->create([
            'customer_id' => $customer->getKey(),
            'proposal_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(30)->toDateString(),
            'subject' => 'Pengadaan Infrastruktur IT dan Implementasi CRM',
            'description' => 'Penawaran solusi terintegrasi untuk kebutuhan digitalisasi proses penjualan.',
            'scope_of_work' => "1. Pengadaan perangkat\n2. Implementasi aplikasi\n3. Pelatihan pengguna",
            'terms_and_conditions' => 'Pembayaran 30 hari setelah invoice diterbitkan.',
            'status' => ProposalStatus::Accepted,
            'currency' => 'IDR',
        ]);

        foreach ($products as $index => $product) {
            $proposal->items()->create([
                'product_id' => $product->getKey(),
                'sku' => $product->sku,
                'name' => $product->name,
                'unit' => $product->unit,
                'quantity' => $index === 0 ? 2 : 1,
                'unit_price' => $product->default_price,
                'tax_rate' => 11,
                'sort_order' => $index,
            ]);
        }

        $quotation = app(ConvertProposalToQuotation::class)->handle($proposal);
        $quotation->forceFill(['status' => QuotationStatus::Sent])->save();

        $incomingPo = app(ConvertQuotationToIncomingPo::class)->handle($quotation, [
            'customer_po_number' => 'CPO/DEMO/2026/001',
        ]);
        $incomingPo->forceFill(['status' => IncomingPoStatus::Confirmed])->save();

        if ($supplier !== null) {
            app(ConvertIncomingPoToPurchaseOrder::class)->handle($incomingPo, $supplier);
        }

        $deliveryOrder = app(ConvertIncomingPoToDeliveryOrder::class)->handle($incomingPo);
        $deliveryOrder->forceFill([
            'status' => DeliveryOrderStatus::Delivered,
            'received_by' => 'Bagian Pengadaan',
        ])->save();

        $invoice = app(CreateInvoiceFromDocument::class)->handle($deliveryOrder);
        $invoice->forceFill(['status' => InvoiceStatus::Sent])->save();

        app(RecordInvoicePayment::class)->handle($invoice, [
            'amount' => round((float) $invoice->grand_total * 0.4, 2),
            'reference' => 'DEMO-TRX-001',
        ]);
    }
}
