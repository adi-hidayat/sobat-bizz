<?php

namespace App\Filament\Resources\Regions\Schemas;

use App\Models\Region;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

final class RegionForm
{
    /**
     * @var array<string, string>
     */
    private const PARENT_LEVELS = [
        Region::LEVEL_STATE => Region::LEVEL_COUNTRY,
        Region::LEVEL_CITY => Region::LEVEL_STATE,
        Region::LEVEL_DISTRICT => Region::LEVEL_CITY,
        Region::LEVEL_VILLAGE => Region::LEVEL_DISTRICT,
    ];

    public static function configure(Schema $schema, string $level): Schema
    {
        $parentLevel = self::PARENT_LEVELS[$level] ?? null;

        $fields = [
            TextInput::make('name')
                ->label(self::nameLabel($level))
                ->required()
                ->maxLength(150),
            TextInput::make('code')
                ->label(__('common.fields.code'))
                ->maxLength(20)
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule
                        ->where('level', $level)
                        ->where('source', $get('source') ?? 'manual'),
                ),
        ];

        if ($level !== Region::LEVEL_COUNTRY) {
            $fields[] = Select::make('country_id')
                ->label(__('common.fields.country'))
                ->relationship(
                    'country',
                    'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('level', Region::LEVEL_COUNTRY)
                        ->orderBy('name'),
                )
                ->searchable()
                ->required()
                ->default(fn (): ?int => Region::query()
                    ->where('level', Region::LEVEL_COUNTRY)
                    ->where('code', 'ID')
                    ->value('id'));
        }

        if ($parentLevel !== null && $level !== Region::LEVEL_STATE) {
            $fields[] = Select::make('parent_id')
                ->label(self::parentLabel($level))
                ->relationship(
                    'parent',
                    'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('level', $parentLevel)
                        ->orderBy('name'),
                )
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set): void {
                    $parent = Region::query()->find($state);

                    if ($parent === null) {
                        return;
                    }

                    $set('country_id', $parent->level === Region::LEVEL_COUNTRY
                        ? $parent->getKey()
                        : $parent->country_id);
                });
        }

        if ($level === Region::LEVEL_VILLAGE) {
            $fields[] = TextInput::make('postal_code')
                ->label(__('common.fields.postal_code'))
                ->maxLength(10);
        }

        if (in_array($level, [Region::LEVEL_COUNTRY, Region::LEVEL_STATE, Region::LEVEL_CITY], true)) {
            $fields[] = TextInput::make('latitude')
                ->label(__('common.fields.latitude'))
                ->numeric();
            $fields[] = TextInput::make('longitude')
                ->label(__('common.fields.longitude'))
                ->numeric();
        }

        $fields[] = Hidden::make('source')->default('manual');

        return $schema
            ->components([
                Section::make(__('resources/region.form.section_title'))
                    ->schema($fields)
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function nameLabel(string $level): string
    {
        return match ($level) {
            Region::LEVEL_COUNTRY => __('resources/region.form.name.country'),
            Region::LEVEL_STATE => __('resources/region.form.name.state'),
            Region::LEVEL_CITY => __('resources/region.form.name.city'),
            Region::LEVEL_DISTRICT => __('resources/region.form.name.district'),
            default => __('resources/region.form.name.village'),
        };
    }

    public static function parentLabel(string $level): string
    {
        return match ($level) {
            Region::LEVEL_CITY => __('common.region_levels.state'),
            Region::LEVEL_DISTRICT => __('common.region_levels.city'),
            Region::LEVEL_VILLAGE => __('common.region_levels.district'),
            default => __('common.fields.parent'),
        };
    }
}
