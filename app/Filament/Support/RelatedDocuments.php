<?php

namespace App\Filament\Support;

use App\Models\DocumentLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class RelatedDocuments
{
    /**
     * Ringkasan dokumen asal untuk ditampilkan pada infolist.
     */
    public static function sources(Model $record): string
    {
        if (! method_exists($record, 'documentLinksAsTarget')) {
            return '-';
        }

        return $record->documentLinksAsTarget()
            ->with('source')
            ->get()
            ->map(fn (DocumentLink $link): ?string => self::describe($link->source))
            ->filter()
            ->implode(' · ') ?: '-';
    }

    /**
     * Ringkasan dokumen turunan yang sudah dibuat dari dokumen ini.
     */
    public static function targets(Model $record): string
    {
        if (! method_exists($record, 'documentLinksAsSource')) {
            return '-';
        }

        return $record->documentLinksAsSource()
            ->with('target')
            ->get()
            ->map(fn (DocumentLink $link): ?string => self::describe($link->target))
            ->filter()
            ->implode(' · ') ?: '-';
    }

    private static function describe(?Model $model): ?string
    {
        if ($model === null) {
            return null;
        }

        $number = null;

        if (method_exists($model, 'documentType')) {
            $number = $model->getAttribute($model->documentType()->numberAttribute());
        }

        return trim(sprintf(
            '%s %s',
            Str::headline($model->getMorphClass()),
            $number ?? ('#'.$model->getKey()),
        ));
    }
}
