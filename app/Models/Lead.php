<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\LeadStatus;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasDocumentNumber;
    use HasFactory;
    use RecordsActivity;
    use SoftDeletes;

    protected $fillable = [
        'lead_number',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'industry',
        'lead_source_id',
        'status',
        'assigned_to',
        'follow_up_date',
        'notes',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'follow_up_date' => 'date',
            'converted_at' => 'datetime',
        ];
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Lead;
    }

    public function assignedSalesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function scopeConvertible(Builder $query): Builder
    {
        return $query->whereIn('status', [
            LeadStatus::New->value,
            LeadStatus::Contacted->value,
            LeadStatus::Qualified->value,
        ]);
    }

    public function scopePendingFollowUp(Builder $query): Builder
    {
        return $query->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<=', now())
            ->whereNotIn('status', [
                LeadStatus::Converted->value,
                LeadStatus::Lost->value,
            ]);
    }

    public function isConvertible(): bool
    {
        return $this->status->isConvertible() && $this->customer === null;
    }
}
