<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\LeadSource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
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
                                Section::make(__('resources/lead.sections.identity'))
                                    ->schema([
                                        TextInput::make('lead_number')
                                            ->label(__('resources/lead.fields.lead_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('lead_type')
                                            ->label(__('resources/lead.fields.lead_type'))
                                            ->options(LeadType::class)
                                            ->default(LeadType::Company)
                                            ->required()
                                            ->live(),
                                        TextInput::make('company_name')
                                            ->label(__('common.fields.company_name'))
                                            ->visible(fn (Get $get): bool => self::hasType($get, LeadType::Company))
                                            ->required(fn (Get $get): bool => self::hasType($get, LeadType::Company))
                                            ->maxLength(255),
                                        TextInput::make('contact_person')
                                            ->label(fn (Get $get): string => self::hasType($get, LeadType::Individual)
                                                ? __('resources/lead.fields.person_name')
                                                : __('resources/lead.fields.contact_person'))
                                            ->required(fn (Get $get): bool => self::hasType($get, LeadType::Individual))
                                            ->maxLength(255),
                                        TextInput::make('job_title')
                                            ->label(__('common.fields.job_title'))
                                            ->maxLength(255),
                                        TextInput::make('industry')
                                            ->label(__('common.fields.industry'))
                                            ->maxLength(255),
                                    ])
                                    ->columns(2),

                                Section::make(__('resources/lead.sections.qualification'))
                                    ->schema([
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(LeadStatus::class)
                                            ->default(LeadStatus::New)
                                            ->required()
                                            ->live(),
                                        Select::make('lead_rating_id')
                                            ->label(__('resources/lead.fields.rating'))
                                            ->relationship('rating', 'name')
                                            ->searchable()
                                            ->preload(),
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
                                        Select::make('assigned_to')
                                            ->label(__('resources/lead.fields.sales_person'))
                                            ->relationship('assignedSalesPerson', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('salesTeam')
                                            ->label(__('resources/lead.fields.sales_team'))
                                            ->relationship(
                                                'salesTeam',
                                                'name',
                                                modifyQueryUsing: function (Builder $query, Get $get): Builder {
                                                    $ownerId = $get('assigned_to');

                                                    return filled($ownerId) ? $query->whereKeyNot($ownerId) : $query;
                                                },
                                            )
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        TextInput::make('estimated_value')
                                            ->label(__('resources/lead.fields.estimated_value'))
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp'),
                                        DatePicker::make('expected_close_date')
                                            ->label(__('common.fields.expected_close_date')),
                                        DatePicker::make('follow_up_date')
                                            ->label(__('common.fields.follow_up_date')),
                                        Textarea::make('lost_reason')
                                            ->label(__('resources/lead.fields.lost_reason'))
                                            ->rows(3)
                                            ->visible(fn (Get $get): bool => self::requiresLostReason($get))
                                            ->required(fn (Get $get): bool => self::requiresLostReason($get))
                                            ->columnSpanFull(),
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

    private static function hasType(Get $get, LeadType $type): bool
    {
        $value = $get('lead_type');

        return $value === $type || $value === $type->value;
    }

    private static function requiresLostReason(Get $get): bool
    {
        $value = $get('status');

        if ($value instanceof LeadStatus) {
            $value = $value->value;
        }

        return in_array($value, [LeadStatus::Lost->value, LeadStatus::Unqualified->value], true);
    }
}
