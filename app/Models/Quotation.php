<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\QuotationStatus;
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

class Quotation extends Model
{
    use CascadesDocumentDeletion;
    use HasAttachments;
    use HasAuditColumns;
    use HasDocumentLinks;
    use HasDocumentNumber;
    use HasFactory;
    use HasItems;
    use HasTotals;
    use RecordsActivity;

    protected $fillable = [
        'quotation_number',
        'customer_id',
        'version',
        'quotation_date',
        'valid_until',
        'currency',
        'payment_terms',
        'delivery_terms',
        'notes',
        'terms_and_conditions',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'version' => 'integer',
            'status' => QuotationStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Quotation;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function scopeEditable(Builder $query): Builder
    {
        return $query->where('status', QuotationStatus::Draft->value);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', QuotationStatus::Accepted->value);
    }
}
