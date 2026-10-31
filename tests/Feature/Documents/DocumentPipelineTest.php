<?php

use App\Actions\ConvertIncomingPoToDeliveryOrder;
use App\Actions\ConvertIncomingPoToPurchaseOrder;
use App\Actions\ConvertLeadToCustomer;
use App\Actions\ConvertProposalToQuotation;
use App\Actions\ConvertQuotationToIncomingPo;
use App\Actions\CreateInvoiceFromDocument;
use App\Actions\ReviseQuotation;
use App\Enums\IncomingPoStatus;
use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Models\Customer;
use App\Models\IncomingPo;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\Tax;

it('mengonversi lead menjadi customer tanpa menghapus data lead', function () {
    $lead = Lead::factory()->qualified()->create(['company_name' => 'PT Uji Konversi']);

    $customer = app(ConvertLeadToCustomer::class)->handle($lead);

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->company_name)->toBe('PT Uji Konversi')
        ->and($customer->lead_id)->toBe($lead->getKey())
        ->and($lead->refresh()->status)->toBe(LeadStatus::Converted)
        ->and($lead->converted_at)->not->toBeNull();

    $this->assertDatabaseHas('document_links', [
        'source_type' => 'lead',
        'source_id' => $lead->getKey(),
        'target_type' => 'customer',
        'target_id' => $customer->getKey(),
    ]);
});

it('menyalin item proposal ke quotation sebagai snapshot baru', function () {
    $proposal = Proposal::factory()->create();
    $proposal->items()->create([
        'name' => 'Website Development',
        'quantity' => 1,
        'unit' => 'Project',
        'unit_price' => 15_000_000,
        'tax_rate' => 11,
    ]);

    $quotation = app(ConvertProposalToQuotation::class)->handle($proposal);

    expect($quotation->items)->toHaveCount(1)
        ->and($quotation->items->first()->name)->toBe('Website Development')
        ->and($quotation->items->first()->getKey())->not->toBe($proposal->items->first()->getKey())
        ->and((float) $quotation->grand_total)->toBe(16_650_000.0)
        ->and($proposal->items()->count())->toBe(1);

    $this->assertDatabaseHas('document_item_links', [
        'source_item_id' => $proposal->items->first()->getKey(),
        'target_item_id' => $quotation->items->first()->getKey(),
    ]);
});

it('menyalin beberapa pajak item saat konversi dokumen', function () {
    $ppn = Tax::factory()->create(['name' => 'PPN 11%', 'value' => 11]);
    $pph = Tax::factory()->create(['name' => 'PPh 23', 'value' => 2]);

    $proposal = Proposal::factory()->create();
    $proposal->items()->create([
        'name' => 'Implementasi',
        'quantity' => 1,
        'unit_price' => 10_000_000,
        'tax_ids' => [$ppn->getKey(), $pph->getKey()],
    ]);

    $quotation = app(ConvertProposalToQuotation::class)->handle($proposal);
    $item = $quotation->items->first();

    expect($item->taxes)->toHaveCount(2)
        ->and($item->taxes->pluck('tax_id')->all())->toBe([$ppn->getKey(), $pph->getKey()])
        ->and((float) $item->tax_amount)->toBe(1_300_000.0)
        ->and((float) $quotation->grand_total)->toBe(11_300_000.0);
});

it('menjalankan alur quotation sampai invoice dengan quantity yang konsisten', function () {
    $customer = Customer::factory()->create(['payment_term_days' => 30]);

    $proposal = Proposal::factory()->create(['customer_id' => $customer->getKey()]);
    $proposal->items()->create([
        'name' => 'Lisensi Tahunan',
        'quantity' => 10,
        'unit_price' => 1_000_000,
        'tax_rate' => 11,
    ]);

    $quotation = app(ConvertProposalToQuotation::class)->handle($proposal);
    $incomingPo = app(ConvertQuotationToIncomingPo::class)->handle($quotation);

    expect($incomingPo->customer_id)->toBe($customer->getKey())
        ->and((float) $incomingPo->grand_total)->toBe(11_100_000.0);

    $incomingPo->forceFill(['status' => IncomingPoStatus::Confirmed])->save();

    $supplier = Supplier::factory()->create();
    $purchaseOrder = app(ConvertIncomingPoToPurchaseOrder::class)->handle($incomingPo, $supplier);

    expect($purchaseOrder->supplier_id)->toBe($supplier->getKey())
        ->and($purchaseOrder->items)->toHaveCount(1);

    $deliveryOrder = app(ConvertIncomingPoToDeliveryOrder::class)->handle($incomingPo);

    expect((float) $deliveryOrder->items->first()->quantity)->toBe(10.0)
        ->and($incomingPo->refresh()->status)->toBe(IncomingPoStatus::Fulfilled);

    $invoice = app(CreateInvoiceFromDocument::class)->handle($deliveryOrder);

    expect((float) $invoice->grand_total)->toBe(11_100_000.0)
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and((float) $invoice->outstanding_balance)->toBe(11_100_000.0);
});

it('membuat revisi quotation dengan nomor dan versi tersendiri', function () {
    $quotation = Quotation::factory()->sent()->create();
    $quotation->items()->create([
        'name' => 'Paket Implementasi',
        'quantity' => 1,
        'unit_price' => 5_000_000,
        'tax_rate' => 0,
    ]);

    $revision = app(ReviseQuotation::class)->handle($quotation);

    expect($revision->version)->toBe(2)
        ->and($revision->quotation_number)->not->toBe($quotation->quotation_number)
        ->and($revision->items)->toHaveCount(1)
        ->and($revision->items->first()->getKey())->not->toBe($quotation->items->first()->getKey());
});

it('mencegah pembuatan dua incoming PO dari quotation yang sama', function () {
    $quotation = Quotation::factory()->accepted()->create();
    $quotation->items()->create([
        'name' => 'Jasa',
        'quantity' => 1,
        'unit_price' => 1_000_000,
        'tax_rate' => 0,
    ]);

    app(ConvertQuotationToIncomingPo::class)->handle($quotation);
    app(ConvertQuotationToIncomingPo::class)->handle($quotation);
})->throws(InvalidArgumentException::class);

it('mendukung partial delivery lewat beberapa delivery order', function () {
    $incomingPo = IncomingPo::factory()->confirmed()->create();
    $incomingPo->items()->create([
        'name' => 'Server',
        'quantity' => 6,
        'unit_price' => 10_000_000,
        'tax_rate' => 0,
    ]);

    $first = app(ConvertIncomingPoToDeliveryOrder::class)->handle($incomingPo);
    $first->items->first()->forceFill(['quantity' => 3])->save();

    expect((float) $first->items()->first()->quantity)->toBe(3.0);

    $second = app(ConvertIncomingPoToDeliveryOrder::class)->handle($incomingPo);

    expect((float) $second->items->first()->quantity)->toBe(3.0);

    $incomingPo->refresh();

    expect($incomingPo->status)->toBe(IncomingPoStatus::Fulfilled);
});
