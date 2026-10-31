<?php

namespace App\Models\Concerns;

use App\Enums\DocumentType;
use App\Services\Documents\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Model;

trait HasDocumentNumber
{
    abstract public function documentType(): DocumentType;

    protected static function bootHasDocumentNumber(): void
    {
        static::creating(function (Model $model): void {
            $attribute = $model->documentType()->numberAttribute();

            if (blank($model->getAttribute($attribute))) {
                $model->setAttribute(
                    $attribute,
                    app(DocumentNumberGenerator::class)->generate($model->documentType()),
                );
            }
        });
    }
}
