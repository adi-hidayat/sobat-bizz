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

    protected $fillable = [
        'name',
        'color',
        'text_color',
        'description',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
