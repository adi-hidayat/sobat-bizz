<?php

namespace App\Models;

use App\Enums\DeliveryOrderStatus;
use App\Enums\DocumentType;
use App\Models\Concerns\CascadesDocumentDeletion;
use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\HasAuditColumns;
use App\Models\Concerns\HasDocumentLinks;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\HasItems;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrder extends Model
{
    use CascadesDocumentDeletion;
    use HasAttachments;
    use HasAuditColumns;
    use HasDocumentLinks;
    use HasDocumentNumber;
    use HasFactory;
    use HasItems;
    use RecordsActivity;

    protected $fillable = [
        'do_number',
        'customer_id',
        'delivery_date',
        'shipping_address',
        'delivery_method',
        'received_by',
        'delivery_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'status' => DeliveryOrderStatus::class,
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::DeliveryOrder;
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
