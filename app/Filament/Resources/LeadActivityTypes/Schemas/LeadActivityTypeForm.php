<?php

namespace App\Filament\Resources\LeadActivityTypes\Schemas;

use App\Enums\LeadActivityGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadActivityTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/lead_activity_type.sections.information'))
                    ->schema([
                        TextInput::make('key')
                            ->label(__('resources/lead_activity_type.fields.key'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->disabledOn('edit')
                            ->columnSpan(1),
                        TextInput::make('name')
                            ->label(__('common.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(1),
                        Select::make('group')
                            ->label(__('resources/lead_activity_type.fields.group'))
                            ->options(LeadActivityGroup::class)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        TextInput::make('sort_order')
                            ->label(__('resources/lead_activity_type.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label(__('resources/lead_activity_type.fields.is_active'))
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
