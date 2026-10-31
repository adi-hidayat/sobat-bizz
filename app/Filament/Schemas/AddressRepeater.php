<?php

namespace App\Filament\Schemas;

use App\Models\Address;
use App\Models\Region;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

final class AddressRepeater
{
    public static function make(string $relationship = 'addresses'): Repeater
    {
        return Repeater::make($relationship)
            ->relationship()
            ->hiddenLabel()
            ->schema([
                Select::make('type')
                    ->label(__('common.fields.address_type'))
                    ->options([
                        Address::TYPE_BILLING => __('common.address_types.billing'),
                        Address::TYPE_SHIPPING => __('common.address_types.shipping'),
                        Address::TYPE_OFFICE => __('common.address_types.office'),
                        Address::TYPE_OTHER => __('common.address_types.other'),
                    ])
                    ->default(Address::TYPE_BILLING)
                    ->required(),
                TextInput::make('label')
                    ->label(__('common.fields.label'))
                    ->placeholder(__('common.placeholders.example_head_office'))
                    ->maxLength(255),
                TextInput::make('contact_name')
                    ->label(__('common.fields.contact_name'))
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('common.fields.phone'))
                    ->tel()
                    ->maxLength(50),
                Textarea::make('address_line')
                    ->label(__('common.fields.address_line'))
                    ->rows(2)
                    ->columnSpanFull(),

                Select::make('country_id')
                    ->label(__('common.fields.country'))
                    ->options(fn (): array => Region::query()->countries()->orderBy('name')->pluck('name', 'id')->all())
                    ->default(fn (): ?int => Region::query()->countries()->where('code', 'ID')->value('id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => self::clearFrom($set, 'country_id'))
                    ->required(),

                Select::make('state_id')
                    ->label(__('common.fields.state'))
                    ->options(fn (Get $get): array => filled($get('country_id'))
                        ? Region::query()
                            ->where('level', Region::LEVEL_STATE)
                            ->where('parent_id', $get('country_id'))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()
                        : [])
                    ->searchable()
                    ->live()
                    ->disabled(fn (Get $get): bool => blank($get('country_id')))
                    ->afterStateUpdated(fn (Set $set) => self::clearFrom($set, 'state_id')),

                self::citySelect(),

                self::deepSelect('district_id', __('common.fields.district'), Region::LEVEL_DISTRICT, 'city_id'),

                self::villageSelect(),

                TextInput::make('postal_code')
                    ->label(__('common.fields.postal_code'))
                    ->maxLength(10),

                Toggle::make('is_primary')
                    ->label(__('common.fields.is_primary_address')),

                Textarea::make('notes')
                    ->label(__('common.fields.address_notes'))
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->defaultItems(0)
            ->addActionLabel(__('common.actions.add_address'))
            ->collapsible()
            ->itemLabel(function (array $state): ?string {
                $type = match ($state['type'] ?? null) {
                    Address::TYPE_BILLING => __('common.address_types.billing'),
                    Address::TYPE_SHIPPING => __('common.address_types.shipping'),
                    Address::TYPE_OFFICE => __('common.address_types.office'),
                    default => __('common.address_types.default'),
                };

                return trim($type.' '.($state['label'] ?? $state['address_line'] ?? ''));
            })
            ->columnSpanFull();
    }

    private static function citySelect(): Select
    {
        return self::deepSelect('city_id', __('common.fields.city'), Region::LEVEL_CITY, 'state_id')
            ->afterStateUpdated(fn (Set $set) => self::clearFrom($set, 'city_id'));
    }

    private static function deepSelect(string $field, string $label, string $level, string $parentField): Select
    {
        return Select::make($field)
            ->label($label)
            ->searchable()
            ->live()
            ->disabled(fn (Get $get): bool => blank($get($parentField)))
            ->getSearchResultsUsing(function (string $search, Get $get) use ($level, $parentField): array {
                $parentId = $get($parentField);

                if (blank($parentId)) {
                    return [];
                }

                return Region::query()
                    ->where('level', $level)
                    ->where('parent_id', $parentId)
                    ->where('name', 'like', "%{$search}%")
                    ->orderBy('name')
                    ->limit(50)
                    ->pluck('name', 'id')
                    ->all();
            })
            ->getOptionLabelUsing(fn ($value): ?string => Region::find($value)?->name)
            ->afterStateUpdated(fn (Set $set) => self::clearFrom($set, $field));
    }

    private static function villageSelect(): Select
    {
        return Select::make('village_id')
            ->label(__('common.fields.village'))
            ->searchable()
            ->live()
            ->disabled(fn (Get $get): bool => blank($get('district_id')))
            ->getSearchResultsUsing(function (string $search, Get $get): array {
                $parentId = $get('district_id');

                if (blank($parentId)) {
                    return [];
                }

                return Region::query()
                    ->where('level', Region::LEVEL_VILLAGE)
                    ->where('parent_id', $parentId)
                    ->where('name', 'like', "%{$search}%")
                    ->orderBy('name')
                    ->limit(50)
                    ->pluck('name', 'id')
                    ->all();
            })
            ->getOptionLabelUsing(fn ($value): ?string => Region::find($value)?->name)
            ->afterStateUpdated(function (Set $set, ?int $state): void {
                $set('postal_code', $state !== null ? Region::find($state)?->postal_code : null);
            });
    }

    /**
     * Mengosongkan pilihan wilayah di bawah level yang diubah.
     */
    private static function clearFrom(Set $set, string $field): void
    {
        $levels = ['country_id', 'state_id', 'city_id', 'district_id', 'village_id'];
        $index = array_search($field, $levels, true);

        if ($index === false) {
            return;
        }

        foreach (array_slice($levels, $index + 1) as $child) {
            $set($child, null);
        }

        $set('postal_code', null);
    }
}
