<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait RecordsActivity
{
    protected static function bootRecordsActivity(): void
    {
        static::created(function (Model $model): void {
            $model->logActivity('created', 'created');
        });

        static::updated(function (Model $model): void {
            $changed = Arr::except($model->getChanges(), ['updated_at', 'updated_by']);

            if ($changed === []) {
                return;
            }

            $event = array_key_exists('status', $changed) ? 'status_changed' : 'updated';

            $model->logActivity($event, $event, ['changes' => $changed]);
        });

        static::deleted(function (Model $model): void {
            $model->logActivity('deleted', 'deleted');
        });
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function logActivity(string $event, string $verb, array $properties = []): ActivityLog
    {
        return ActivityLog::create([
            'subject_type' => $this->getMorphClass(),
            'subject_id' => $this->getKey(),
            'event' => $event,
            'description' => sprintf('%s %s', class_basename($this), $verb),
            'properties' => $properties === [] ? null : $properties,
            'causer_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
