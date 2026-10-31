<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\LeadRating;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Concerns\HasDocumentNumber;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'lead_type',
        'company_name',
        'contact_person',
        'job_title',
        'email',
        'phone',
        'address',
        'industry',
        'lead_source_id',
        'status',
        'rating',
        'estimated_value',
        'expected_close_date',
        'assigned_to',
        'follow_up_date',
        'notes',
        'lost_reason',
        'last_contacted_at',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'lead_type' => LeadType::class,
            'status' => LeadStatus::class,
            'rating' => LeadRating::class,
            'estimated_value' => 'decimal:2',
            'expected_close_date' => 'date',
            'follow_up_date' => 'date',
            'last_contacted_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    /**
     * Nama yang ditampilkan: perusahaan untuk lead B2B, nama orang untuk lead perorangan.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: ($this->contact_person ?: $this->lead_number);
    }

    public function isCompany(): bool
    {
        return $this->lead_type === LeadType::Company;
    }

    public function isIndividual(): bool
    {
        return $this->lead_type === LeadType::Individual;
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

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('occurred_at');
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

    public function scopeOverdueFollowUp(Builder $query): Builder
    {
        return $query->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<', now()->toDateString())
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
