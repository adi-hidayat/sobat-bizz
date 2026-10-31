<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

/**
 * The framework translation files are maintained separately, so parity is only
 * enforced for the application-owned translation files.
 */
function applicationTranslationKeys(string $locale): array
{
    $excluded = ['auth.php', 'passwords.php', 'pagination.php', 'validation.php'];
    $root = lang_path($locale);

    return collect(File::allFiles($root))
        ->reject(fn ($file): bool => in_array($file->getFilename(), $excluded, true))
        ->flatMap(function ($file) use ($locale, $root): array {
            $relative = str_replace('\\', '/', str_replace($root.DIRECTORY_SEPARATOR, '', $file->getPathname()));
            $key = str_replace('/', '.', preg_replace('/\.php$/', '', $relative));
            $values = require $file->getPathname();

            return array_keys(Arr::dot($values, $key.'.'));
        })
        ->sort()
        ->values()
        ->all();
}

it('has the same translation keys for indonesian and english', function (): void {
    expect(applicationTranslationKeys('id'))->toBe(applicationTranslationKeys('en'));
});

it('does not have blank application translations', function (string $locale): void {
    $root = lang_path($locale);

    $blank = collect(File::allFiles($root))
        ->reject(fn ($file): bool => in_array($file->getFilename(), ['auth.php', 'passwords.php', 'pagination.php', 'validation.php'], true))
        ->flatMap(function ($file) use ($root): array {
            $relative = str_replace('\\', '/', str_replace($root.DIRECTORY_SEPARATOR, '', $file->getPathname()));
            $prefix = str_replace('/', '.', preg_replace('/\.php$/', '', $relative));
            $values = Arr::dot(require $file->getPathname(), $prefix.'.');

            return array_keys(array_filter($values, fn ($value): bool => blank($value)));
        })
        ->values()
        ->all();

    expect($blank)->toBe([]);
})->with(['id', 'en']);
