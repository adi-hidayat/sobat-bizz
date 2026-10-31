<?php

namespace App\Models;

use Database\Factories\LeadRatingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadRating extends Model
{
    /** @use HasFactory<LeadRatingFactory> */
    use HasFactory;

    /**
     * Warna badge yang didukung Filament.
     *
     * @var list<string>
     */
    public const COLORS = ['gray', 'primary', 'info', 'success', 'warning', 'danger'];

    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    public static function colorOptions(): array
    {
        return collect(self::COLORS)
            ->mapWithKeys(fn (string $color): array => [$color => __("resources/lead_rating.colors.{$color}")])
            ->all();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
