<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class RecordInvoicePayment
{
    /**
     * Catat pembayaran dan alokasikan langsung ke invoice.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Invoice $invoice, array $data): Payment
    {
        $amount = round((float) ($data['amount'] ?? 0), 2);

        if ($amount <= 0) {
            throw new InvalidArgumentException(__('documents.errors.payment_amount_must_be_positive'));
        }

        if ($amount > (float) $invoice->outstanding_balance + 0.0001) {
            throw new InvalidArgumentException(__('documents.errors.payment_exceeds_outstanding'));
        }

        return DB::transaction(function () use ($invoice, $amount, $data): Payment {
            $payment = Payment::query()->create([
                'customer_id' => $invoice->customer_id,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'method' => $data['method'] ?? PaymentMethod::BankTransfer,
                'amount' => $amount,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $payment->allocations()->create([
                'invoice_id' => $invoice->getKey(),
                'amount' => $amount,
            ]);

            if ($invoice->status === InvoiceStatus::Draft) {
                $invoice->forceFill(['status' => InvoiceStatus::Sent])->save();
            }

            $invoice->recalculatePayments();

            $invoice->logActivity('payment_received', 'payment received', [
                'payment_id' => $payment->getKey(),
                'payment_number' => $payment->payment_number,
                'amount' => $amount,
            ]);

            return $payment;
        });
    }
}
