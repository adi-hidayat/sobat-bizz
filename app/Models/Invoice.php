<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Concerns\CascadesDocumentDeletion;
use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\HasAuditColumns;
use App\Models\Concerns\HasDocumentLinks;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\HasItems;
use App\Models\Concerns\HasTotals;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use CascadesDocumentDeletion;
    use HasAttachments;
    use HasAuditColumns;
    use HasDocumentLinks;
    use HasDocumentNumber;
    use HasFactory;
    use HasItems;
    use HasTotals {
        recalculateTotals as recalculateDocumentTotals;
    }
    use RecordsActivity;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'due_date',
        'payment_terms',
        'currency',
        'notes',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'amount_paid',
        'outstanding_balance',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'status' => InvoiceStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Invoice;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'payment_allocations')
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Total dokumen selalu diikuti oleh status pembayaran.
     *
     * @return array<string, float>
     */
    public function recalculateTotals(): array
    {
        $totals = $this->recalculateDocumentTotals();

        $this->recalculatePayments();

        return $totals;
    }

    public function recalculatePayments(): void
    {
        $paid = round((float) $this->allocations()->sum('amount'), 2);
        $grandTotal = (float) $this->grand_total;
        $outstanding = round(max($grandTotal - $paid, 0), 2);

        $attributes = [
            'amount_paid' => $paid,
            'outstanding_balance' => $outstanding,
        ];

        if (! $this->status->isCancelled() && $this->status !== InvoiceStatus::Draft) {
            $attributes['status'] = match (true) {
                $grandTotal > 0 && $outstanding <= 0 => InvoiceStatus::Paid,
                $paid > 0 => InvoiceStatus::PartiallyPaid,
                $this->due_date !== null && $this->due_date->isPast() => InvoiceStatus::Overdue,
                default => InvoiceStatus::Sent,
            };
        }

        $this->forceFill($attributes)->saveQuietly();
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            InvoiceStatus::Draft->value,
            InvoiceStatus::Cancelled->value,
        ])->where('outstanding_balance', '>', 0);
    }
}
