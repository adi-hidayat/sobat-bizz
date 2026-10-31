<?php

use App\Actions\RecordInvoicePayment;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Validation\ValidationException;

function sentInvoiceWithTotal(float $total = 1_000_000): Invoice
{
    $invoice = Invoice::factory()->sent()->create();
    $invoice->items()->create([
        'name' => 'Jasa Implementasi',
        'quantity' => 1,
        'unit_price' => $total,
        'tax_rate' => 0,
    ]);

    return $invoice->refresh();
}

it('menghitung outstanding balance setelah pembayaran parsial', function () {
    $invoice = sentInvoiceWithTotal(10_000_000);

    app(RecordInvoicePayment::class)->handle($invoice, [
        'amount' => 4_000_000,
        'payment_date' => now()->toDateString(),
    ]);

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::PartiallyPaid)
        ->and((float) $invoice->amount_paid)->toBe(4_000_000.0)
        ->and((float) $invoice->outstanding_balance)->toBe(6_000_000.0);
});

it('menandai invoice lunas ketika sisa tagihan nol', function () {
    $invoice = sentInvoiceWithTotal(5_000_000);

    app(RecordInvoicePayment::class)->handle($invoice, ['amount' => 2_500_000]);
    app(RecordInvoicePayment::class)->handle($invoice->refresh(), ['amount' => 2_500_000]);

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and((float) $invoice->outstanding_balance)->toBe(0.0)
        ->and(Payment::query()->count())->toBe(2);
});

it('menolak pembayaran melebihi sisa tagihan', function () {
    $invoice = sentInvoiceWithTotal(1_000_000);

    app(RecordInvoicePayment::class)->handle($invoice, ['amount' => 2_000_000]);
})->throws(InvalidArgumentException::class);

it('menolak alokasi pembayaran melebihi nominal pembayaran', function () {
    $invoice = sentInvoiceWithTotal(1_000_000);
    $payment = Payment::factory()->create(['amount' => 500_000]);

    PaymentAllocation::query()->create([
        'payment_id' => $payment->getKey(),
        'invoice_id' => $invoice->getKey(),
        'amount' => 750_000,
    ]);
})->throws(ValidationException::class);
