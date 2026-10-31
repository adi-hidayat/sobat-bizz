<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Str;

trait HasEnumLabel
{
    public function getLabel(): string
    {
        return __('enums.'.Str::snake(class_basename($this)).'.'.Str::snake($this->name));
    }
}
