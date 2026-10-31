<?php

namespace App\Filament\Resources\LeadRatings\Schemas;

use App\Models\LeadRating;
use Filament\Forms\Components\Select;
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
                        Select::make('color')
                            ->label(__('resources/lead_rating.fields.color'))
                            ->options(LeadRating::colorOptions())
                            ->default('gray')
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
