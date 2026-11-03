<?php

namespace App\Models;

use App\Enums\LeadActivityGroup;
use Database\Factories\LeadActivityTypeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadActivityType extends Model
{
    /** @use HasFactory<LeadActivityTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'group',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'group' => LeadActivityGroup::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group')->orderBy('sort_order')->orderBy('name');
    }

    public function isTask(): bool
    {
        return $this->group->isTask();
    }

    public function updatesLastContactedAt(): bool
    {
        return $this->group->updatesLastContactedAt();
    }

    /**
     * Pilihan aktivitas untuk form, dikelompokkan berdasarkan grup.
     *
     * @return array<string, array<int, string>>
     */
    public static function groupedOptions(): array
    {
        return static::query()
            ->active()
            ->ordered()
            ->get()
            ->groupBy(fn (LeadActivityType $type): string => $type->group->getLabel())
            ->map(fn ($types): array => $types->mapWithKeys(fn (LeadActivityType $type): array => [
                $type->getKey() => $type->name,
            ])->all())
            ->all();
    }
}
