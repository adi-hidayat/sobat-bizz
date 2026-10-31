<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use App\Models\LeadSource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('lead')
                    ->tabs([
                        Tab::make(__('resources/lead.tabs.information'))
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make(__('resources/lead.sections.information'))
                                    ->schema([
                                        TextInput::make('lead_number')
                                            ->label(__('resources/lead.fields.lead_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        TextInput::make('company_name')
                                            ->label(__('common.fields.company_name'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('contact_person')
                                            ->label(__('resources/lead.fields.contact_person'))
                                            ->maxLength(255),
                                        TextInput::make('industry')
                                            ->label(__('common.fields.industry'))
                                            ->maxLength(255),
                                        Select::make('lead_source_id')
                                            ->label(__('common.fields.lead_source'))
                                            ->relationship(
                                                'leadSource',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                                    ->orderByDesc('is_active')
                                                    ->ordered(),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (LeadSource $record): string => $record->is_active
                                                ? $record->name
                                                : __('resources/lead.options.inactive', ['name' => $record->name]))
                                            ->default(fn (): ?int => LeadSource::query()->active()->ordered()->value('id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(LeadStatus::class)
                                            ->default(LeadStatus::New)
                                            ->required(),
                                        Select::make('assigned_to')
                                            ->label(__('resources/lead.fields.sales_person'))
                                            ->relationship('assignedSalesPerson', 'name')
                                            ->searchable()
                                            ->preload(),
                                        DatePicker::make('follow_up_date')
                                            ->label(__('common.fields.follow_up_date')),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('resources/lead.tabs.contact_and_address'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('resources/lead.sections.contact_and_address'))
                                    ->schema([
                                        TextInput::make('email')
                                            ->label(__('common.fields.email'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('common.fields.phone'))
                                            ->tel()
                                            ->maxLength(50),
                                        Textarea::make('address')
                                            ->label(__('common.fields.address'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('notes')
                                            ->label(__('common.fields.notes'))
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
