@if ($rating)
    <x-filament::badge
        size="sm"
        style="background-color: {{ $rating->color }}; color: {{ $rating->text_color ?: '#ffffff' }};"
    >
        {{ $rating->name }}
    </x-filament::badge>
@else
    <span>-</span>
@endif
