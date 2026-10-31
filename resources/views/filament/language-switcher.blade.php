@php
    $currentLocale = app()->getLocale();
    $locales = config('localization.supported');
@endphp

@if ($isLogin ?? false)
    <div class="flex items-center justify-center gap-3 py-4 text-sm">
        @foreach ($locales as $locale => $label)
            <a
                href="{{ route('locale.switch', $locale) }}"
                @class([
                    'hover:underline',
                    'font-semibold text-primary-600' => $currentLocale === $locale,
                ])
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
@else
    <x-filament::dropdown placement="bottom-end" teleport>
        <x-slot name="trigger">
            <x-filament::icon-button
                :icon="\Filament\Support\Icons\Heroicon::OutlinedLanguage"
                color="gray"
                :label="__('app.language_switcher')"
                :tooltip="__('app.language_switcher')"
            />
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($locales as $locale => $label)
                <x-filament::dropdown.list.item
                    tag="a"
                    :href="route('locale.switch', $locale)"
                    :icon="$currentLocale === $locale ? \Filament\Support\Icons\Heroicon::OutlinedCheck : null"
                    :color="$currentLocale === $locale ? 'primary' : 'gray'"
                >
                    {{ $label }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif
