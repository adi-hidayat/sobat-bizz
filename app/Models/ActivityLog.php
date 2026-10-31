<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Lang;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'event',
        'description',
        'properties',
        'causer_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function getEventLabelAttribute(): string
    {
        $key = 'resources/activity_log.events.'.$this->event;

        return Lang::has($key) ? __($key) : (string) $this->description;
    }

    public function getSubjectTypeLabelAttribute(): string
    {
        $key = 'resources/activity_log.subjects.'.($this->subject_type ?? '');

        return Lang::has($key)
            ? __($key)
            : str((string) ($this->subject_type ?? '-'))->headline()->toString();
    }
}
