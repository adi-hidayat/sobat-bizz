<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    public const TYPE_BILLING = 'billing';

    public const TYPE_SHIPPING = 'shipping';

    public const TYPE_OFFICE = 'office';

    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'type',
        'label',
        'contact_name',
        'phone',
        'address_line',
        'country_id',
        'state_id',
        'city_id',
        'district_id',
        'village_id',
        'postal_code',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'district_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'village_id');
    }

    /**
     * Alamat lengkap dalam satu baris, siap tampil di tabel/PDF.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line,
            $this->village?->name,
            $this->district?->name,
            $this->city?->name,
            $this->state?->name,
            $this->postal_code,
            $this->country?->name,
        ], fn (?string $part): bool => filled($part));

        return implode(', ', $parts);
    }
}
