<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\SupplierStatus;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasDocumentNumber;
    use HasFactory;
    use RecordsActivity;
    use SoftDeletes;

    protected $fillable = [
        'supplier_code',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'npwp',
        'payment_term_days',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => SupplierStatus::class,
            'payment_term_days' => 'integer',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Supplier;
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SupplierStatus::Active->value);
    }
}
