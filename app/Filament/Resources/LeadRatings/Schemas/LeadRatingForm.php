<?php

namespace App\Filament\Resources\LeadRatings\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadRatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/lead_rating.sections.information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        ColorPicker::make('color')
                            ->label(__('resources/lead_rating.fields.color'))
                            ->default('#9ca3af')
                            ->required(),
                        ColorPicker::make('text_color')
                            ->label(__('resources/lead_rating.fields.text_color'))
                            ->default('#ffffff')
                            ->required(),
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
