<?php

namespace App\Models;

use Database\Factories\LeadActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    /** @use HasFactory<LeadActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'lead_activity_type_id',
        'subject',
        'notes',
        'occurred_at',
        'due_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LeadActivity $activity): void {
            $activity->user_id ??= auth()->id();
        });

        static::created(function (LeadActivity $activity): void {
            if (! $activity->activityType?->updatesLastContactedAt()) {
                return;
            }

            $lead = $activity->lead;

            if ($lead === null) {
                return;
            }

            if ($lead->last_contacted_at === null || $activity->occurred_at->gt($lead->last_contacted_at)) {
                $lead->forceFill(['last_contacted_at' => $activity->occurred_at])->saveQuietly();
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(LeadActivityType::class, 'lead_activity_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }

    public function isOverdue(): bool
    {
        return $this->due_at !== null
            && $this->completed_at === null
            && $this->due_at->isPast();
    }
}
