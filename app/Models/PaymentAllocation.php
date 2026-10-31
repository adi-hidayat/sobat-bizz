<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PaymentAllocation $allocation): void {
            $payment = $allocation->payment;

            if ($payment === null) {
                return;
            }

            $otherAllocations = $payment->allocations()
                ->when($allocation->exists, fn ($query) => $query->whereKeyNot($allocation->getKey()))
                ->sum('amount');

            if ((float) $otherAllocations + (float) $allocation->amount > (float) $payment->amount + 0.0001) {
                throw ValidationException::withMessages([
                    'amount' => __('documents.errors.allocation_exceeds_payment'),
                ]);
            }
        });

        static::saved(fn (PaymentAllocation $allocation) => $allocation->invoice?->recalculatePayments());
        static::deleted(fn (PaymentAllocation $allocation) => $allocation->invoice?->recalculatePayments());
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
