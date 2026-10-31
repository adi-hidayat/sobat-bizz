<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuditColumns
{
    protected static function bootHasAuditColumns(): void
    {
        static::creating(function (Model $model): void {
            if (in_array('created_by', $model->getFillable(), true) && auth()->check()) {
                $model->setAttribute('created_by', $model->getAttribute('created_by') ?? auth()->id());
            }
        });

        static::updating(function (Model $model): void {
            if (in_array('updated_by', $model->getFillable(), true) && auth()->check()) {
                $model->setAttribute('updated_by', auth()->id());
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
