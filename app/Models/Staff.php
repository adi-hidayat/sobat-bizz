<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    /** @use HasFactory<StaffFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'department_id',
        'joined_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Akun pengguna dibuat bersama staff, jadi ikut dihapus saat staff dihapus.
        static::deleted(function (Staff $staff): void {
            $staff->user?->delete();
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
