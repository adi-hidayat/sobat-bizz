<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Attachment $attachment): void {
            $attachment->disk ??= (string) config('crm.attachments.disk');
            $attachment->uploaded_by ??= auth()->id();

            if (blank($attachment->mime_type) && filled($attachment->path) && Storage::disk($attachment->disk)->exists($attachment->path)) {
                $attachment->size ??= Storage::disk($attachment->disk)->size($attachment->path);
                $attachment->mime_type ??= Storage::disk($attachment->disk)->mimeType($attachment->path);
            }
        });

        static::deleted(function (Attachment $attachment): void {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getSizeForHumansAttribute(): string
    {
        $size = (int) $this->size;

        if ($size >= 1048576) {
            return number_format($size / 1048576, 1).' MB';
        }

        return number_format(max($size, 0) / 1024, 0).' KB';
    }
}
