<?php

namespace App\Models\Concerns;

use App\Models\DocumentLink;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocumentLinks
{
    public function documentLinksAsSource(): MorphMany
    {
        return $this->morphMany(DocumentLink::class, 'source');
    }

    public function documentLinksAsTarget(): MorphMany
    {
        return $this->morphMany(DocumentLink::class, 'target');
    }

    /**
     * Dokumen asal yang menghasilkan dokumen ini.
     *
     * @return Collection<int, Model>
     */
    public function sourceDocuments(): Collection
    {
        return $this->documentLinksAsTarget
            ->map(fn (DocumentLink $link): ?Model => $link->source)
            ->filter()
            ->values();
    }

    /**
     * Dokumen asal pertama dengan tipe morph tertentu.
     */
    public function sourceDocument(string $morphClass): ?Model
    {
        return $this->documentLinksAsTarget
            ->firstWhere('source_type', $morphClass)
            ?->source;
    }

    /**
     * Nomor dokumen asal (mis. Quotation) untuk ditampilkan di tabel & PDF.
     */
    public function sourceDocumentNumber(string $morphClass, string $attribute): ?string
    {
        return $this->sourceDocument($morphClass)?->getAttribute($attribute);
    }

    /**
     * Dokumen turunan yang dihasilkan dari dokumen ini.
     *
     * @return Collection<int, Model>
     */
    public function targetDocuments(): Collection
    {
        return $this->documentLinksAsSource
            ->map(fn (DocumentLink $link): ?Model => $link->target)
            ->filter()
            ->values();
    }
}
