@props([
    'label' => null,
    'background' => '#9ca3af',
    'textColor' => '#ffffff',
])

<x-filament::badge
    size="sm"
    style="background-color: {{ $background ?: '#9ca3af' }}; color: {{ $textColor ?: '#ffffff' }};"
>
    {{ $label }}
</x-filament::badge>
