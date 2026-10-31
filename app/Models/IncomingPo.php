<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\IncomingPoStatus;
use App\Models\Concerns\CascadesDocumentDeletion;
use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\HasAuditColumns;
use App\Models\Concerns\HasDocumentLinks;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\HasItems;
use App\Models\Concerns\HasTotals;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingPo extends Model
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
        'incoming_po_number',
        'customer_po_number',
        'customer_id',
        'po_date',
        'expected_delivery_date',
        'currency',
        'payment_terms',
        'notes',
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
            'po_date' => 'date',
            'expected_delivery_date' => 'date',
            'status' => IncomingPoStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::IncomingPo;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }
}
