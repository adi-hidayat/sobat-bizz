<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Enums\DocumentType;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasDocumentNumber;
    use HasFactory;
    use RecordsActivity;
    use SoftDeletes;

    protected $fillable = [
        'customer_code',
        'company_name',
        'customer_type',
        'npwp',
        'email',
        'phone',
        'payment_term_days',
        'credit_limit',
        'status',
        'notes',
        'lead_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'customer_type' => CustomerType::class,
            'status' => CustomerStatus::class,
            'payment_term_days' => 'integer',
            'credit_limit' => 'decimal:2',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Customer;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function billingAddress(): ?Address
    {
        return $this->addresses->firstWhere('type', Address::TYPE_BILLING);
    }

    public function shippingAddress(): ?Address
    {
        return $this->addresses->firstWhere('type', Address::TYPE_SHIPPING);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function incomingPos(): HasMany
    {
        return $this->hasMany(IncomingPo::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomerStatus::Active->value);
    }

    public function outstandingReceivables(): float
    {
        return round((float) $this->invoices()
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('outstanding_balance'), 2);
    }
}
