<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

it('references translation keys that exist in both locales', function (): void {
    $prefixes = ['resources/', 'common.', 'enums.', 'navigation.', 'pages.', 'widgets.', 'documents.', 'app.'];

    $files = collect(File::allFiles(app_path()))
        ->merge(File::allFiles(resource_path('views')));

    $keys = $files
        ->flatMap(function ($file): array {
            $content = File::get($file->getPathname());

            preg_match_all("/__\(\s*'([^']+)'/", $content, $matches);

            return $matches[1];
        })
        ->filter(fn (string $key): bool => Str::startsWith($key, $prefixes) && ! str_ends_with($key, '.'))
        ->unique()
        ->sort()
        ->values()
        ->all();

    foreach (['id', 'en'] as $locale) {
        app()->setLocale($locale);

        $missing = array_values(array_filter($keys, fn (string $key): bool => ! Lang::has($key)));

        expect($missing)->toBe([], "Missing {$locale} translation keys: ".implode(', ', $missing));
    }
});
