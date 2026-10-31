<?php

use App\Enums\DocumentType;
use App\Enums\TaxType;
use App\Models\Customer;
use App\Models\Proposal;
use App\Services\Documents\DocumentNumberGenerator;
use App\Services\Documents\DocumentTotalsCalculator;

it('membuat nomor dokumen berurutan dan unik', function () {
    $generator = app(DocumentNumberGenerator::class);

    $first = $generator->generate(DocumentType::Proposal);
    $second = $generator->generate(DocumentType::Proposal);
    $invoice = $generator->generate(DocumentType::Invoice);

    expect($first)->toBe('PRP/'.now()->year.'/0001')
        ->and($second)->toBe('PRP/'.now()->year.'/0002')
        ->and($invoice)->toBe('INV/'.now()->year.'/0001');
});

it('memberi nomor otomatis pada model dokumen', function () {
    $customer = Customer::factory()->create();

    $first = Proposal::factory()->create(['customer_id' => $customer->getKey()]);
    $second = Proposal::factory()->create(['customer_id' => $customer->getKey()]);

    expect($first->proposal_number)->toMatch('/^PRP\/\d{4}\/\d{4}$/')
        ->and($second->proposal_number)->not->toBe($first->proposal_number);
});

it('menghitung total baris dan total dokumen secara konsisten', function () {
    $calculator = app(DocumentTotalsCalculator::class);

    $line = $calculator->lineTotals(3, 1_000_000, 500_000, 11);

    expect($line['subtotal'])->toBe(2_500_000.0)
        ->and($line['tax_amount'])->toBe(275_000.0)
        ->and($line['total'])->toBe(2_775_000.0);

    $document = $calculator->documentTotals([
        (object) ['subtotal' => 2_500_000, 'tax_amount' => 275_000],
        (object) ['subtotal' => 1_000_000, 'tax_amount' => 110_000],
    ], 250_000);

    expect($document['subtotal'])->toBe(3_500_000.0)
        ->and($document['tax_amount'])->toBe(385_000.0)
        ->and($document['grand_total'])->toBe(3_635_000.0);
});

it('menghitung beberapa pajak pada satu baris item', function () {
    $line = app(DocumentTotalsCalculator::class)->lineTotals(1, 1_000_000, 0, 0, [
        ['type' => TaxType::Percentage, 'value' => 11, 'name' => 'PPN 11%'],
        ['type' => TaxType::Fixed, 'value' => 25_000, 'name' => 'Materai'],
    ]);

    expect($line['subtotal'])->toBe(1_000_000.0)
        ->and($line['tax_amount'])->toBe(135_000.0)
        ->and($line['total'])->toBe(1_135_000.0)
        ->and($line['taxes'])->toHaveCount(2)
        ->and($line['taxes'][0]['amount'])->toBe(110_000.0)
        ->and($line['taxes'][1]['amount'])->toBe(25_000.0);
});

it('tidak membiarkan subtotal baris bernilai negatif', function () {
    $line = app(DocumentTotalsCalculator::class)->lineTotals(1, 100_000, 250_000, 11);

    expect($line['subtotal'])->toBe(0.0)
        ->and($line['tax_amount'])->toBe(0.0)
        ->and($line['total'])->toBe(0.0);
});
