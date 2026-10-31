<?php

namespace App\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;

    public const LEVEL_COUNTRY = 'country';

    public const LEVEL_STATE = 'state';

    public const LEVEL_CITY = 'city';

    public const LEVEL_DISTRICT = 'district';

    public const LEVEL_VILLAGE = 'village';

    protected $fillable = [
        'parent_id',
        'country_id',
        'level',
        'code',
        'name',
        'postal_code',
        'source',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(self::class, 'country_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeOfLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeCountries($query)
    {
        return $query->where('level', self::LEVEL_COUNTRY);
    }

    public function scopeStates($query)
    {
        return $query->where('level', self::LEVEL_STATE);
    }

    public function scopeCities($query)
    {
        return $query->where('level', self::LEVEL_CITY);
    }

    public function scopeDistricts($query)
    {
        return $query->where('level', self::LEVEL_DISTRICT);
    }

    public function scopeVillages($query)
    {
        return $query->where('level', self::LEVEL_VILLAGE);
    }

    public function scopeChildrenOf($query, ?int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Rantai nama wilayah dari level terpilih sampai negara.
     */
    public function fullName(): string
    {
        $names = [$this->name];
        $parent = $this->parent;

        while ($parent !== null) {
            $names[] = $parent->name;
            $parent = $parent->parent;
        }

        return implode(', ', array_reverse($names));
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->postal_code !== null
            ? "{$this->name} ({$this->postal_code})"
            : $this->name;
    }
}
