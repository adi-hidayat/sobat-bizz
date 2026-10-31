<?php

namespace App\Filament\Resources\Taxes\Schemas;

use App\Enums\TaxType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TaxForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/tax.sections.data'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resources/tax.fields.name'))
                            ->placeholder(__('resources/tax.placeholders.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->label(__('common.fields.code'))
                            ->placeholder(__('resources/tax.placeholders.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        Select::make('type')
                            ->label(__('resources/tax.fields.type'))
                            ->options(TaxType::class)
                            ->default(TaxType::Percentage)
                            ->required()
                            ->live(),
                        TextInput::make('value')
                            ->label(__('common.fields.value'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix(fn (Get $get): ?string => $get('type') === TaxType::Fixed->value ? 'Rp' : null)
                            ->suffix(fn (Get $get): ?string => $get('type') === TaxType::Percentage->value ? '%' : null),
                        Toggle::make('is_withholding')
                            ->label(__('resources/tax.fields.is_withholding'))
                            ->helperText(__('resources/tax.helpers.is_withholding')),
                        Toggle::make('is_active')
                            ->label(__('common.fields.is_active'))
                            ->default(true),
                        Toggle::make('is_default')
                            ->label(__('resources/tax.fields.is_default'))
                            ->helperText(__('resources/tax.helpers.is_default')),
                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Textarea::make('description')
                            ->label(__('common.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
