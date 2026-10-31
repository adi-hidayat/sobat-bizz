<?php

namespace App\Filament\Resources\Proposals\Schemas;

use App\Enums\ProposalStatus;
use App\Filament\Schemas\DocumentTotalsFields;
use App\Filament\Schemas\RenderItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('proposal')
                    ->tabs([
                        Tab::make(__('resources/proposal.tabs.information'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('resources/proposal.sections.information'))
                                    ->schema([
                                        TextInput::make('proposal_number')
                                            ->label(__('resources/proposal.fields.proposal_number'))
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder(__('common.placeholders.auto_generated')),
                                        Select::make('customer_id')
                                            ->label(__('common.fields.customer'))
                                            ->relationship('customer', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('proposal_date')
                                            ->label(__('common.fields.proposal_date'))
                                            ->default(now())
                                            ->required(),
                                        DatePicker::make('expiry_date')
                                            ->label(__('common.fields.expiry_date')),
                                        TextInput::make('subject')
                                            ->label(__('resources/proposal.fields.subject'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Select::make('status')
                                            ->label(__('common.fields.status'))
                                            ->options(ProposalStatus::class)
                                            ->default(ProposalStatus::Draft)
                                            ->required(),
                                        TextInput::make('currency')
                                            ->label(__('common.fields.currency'))
                                            ->default('IDR')
                                            ->required()
                                            ->maxLength(3),
                                    ])
                                    ->columns(2),
                                Section::make(__('resources/proposal.sections.scope'))
                                    ->schema([
                                        Textarea::make('description')
                                            ->label(__('common.fields.description'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('scope_of_work')
                                            ->label(__('resources/proposal.fields.scope_of_work'))
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('terms_and_conditions')
                                            ->label(__('common.fields.terms_and_conditions'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('resources/proposal.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Section::make(__('resources/proposal.sections.items'))
                                    ->schema([
                                        RenderItem::make(),
                                    ]),
                            ]),

                        Tab::make(__('resources/proposal.tabs.summary'))
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                DocumentTotalsFields::section(),
                                Section::make(__('resources/proposal.sections.notes'))
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label(__('resources/proposal.fields.internal_notes'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
