<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\PaymentMethod;
use App\Models\Concerns\HasAuditColumns;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Payment extends Model
{
    use HasAuditColumns;
    use HasDocumentNumber;
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'payment_number',
        'customer_id',
        'payment_date',
        'method',
        'amount',
        'reference',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'method' => PaymentMethod::class,
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Payment $payment): void {
            if (! $payment->exists) {
                return;
            }

            $allocated = (float) $payment->allocations()->sum('amount');

            if ((float) $payment->amount + 0.0001 < $allocated) {
                throw ValidationException::withMessages([
                    'amount' => __('documents.errors.payment_below_allocations'),
                ]);
            }
        });

        static::deleting(function (Payment $payment): void {
            $payment->allocations()->get()->each->invoice?->recalculatePayments();
        });
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Payment;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'payment_allocations')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function allocatedAmount(): float
    {
        return round((float) $this->allocations()->sum('amount'), 2);
    }

    public function unallocatedAmount(): float
    {
        return round((float) $this->amount - $this->allocatedAmount(), 2);
    }
}
