<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LeadInfolist
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
                                        TextEntry::make('lead_number')->label(__('resources/lead.fields.lead_number')),
                                        TextEntry::make('lead_type')->label(__('resources/lead.fields.lead_type'))->badge(),
                                        TextEntry::make('company_name')
                                            ->label(__('common.fields.company_name'))
                                            ->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('contact_person')->label(__('resources/lead.fields.contact_person')),
                                        TextEntry::make('job_title')->label(__('common.fields.job_title'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('industry')->label(__('common.fields.industry'))->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),

                                Section::make(__('resources/lead.sections.qualification'))
                                    ->schema([
                                        TextEntry::make('leadSource.name')->label(__('resources/lead.fields.source'))->badge(),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('rating')
                                            ->label(__('resources/lead.fields.rating'))
                                            ->badge()
                                            ->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('estimated_value')->label(__('resources/lead.fields.estimated_value'))->money('IDR'),
                                        TextEntry::make('expected_close_date')
                                            ->label(__('common.fields.expected_close_date'))
                                            ->date('d M Y')
                                            ->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('assignedSalesPerson.name')->label(__('resources/lead.fields.sales')),
                                        TextEntry::make('follow_up_date')->label(__('resources/lead.fields.follow_up'))->date('d M Y'),
                                        TextEntry::make('last_contacted_at')
                                            ->label(__('resources/lead.fields.last_contacted_at'))
                                            ->dateTime('d M Y H:i')
                                            ->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('lost_reason')
                                            ->label(__('resources/lead.fields.lost_reason'))
                                            ->placeholder(__('common.placeholders.none'))
                                            ->columnSpanFull(),
                                        TextEntry::make('converted_at')->label(__('resources/lead.fields.converted_at'))->dateTime('d M Y H:i')->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make(__('resources/lead.tabs.contact_and_address'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make(__('resources/lead.sections.contact_and_address'))
                                    ->schema([
                                        TextEntry::make('email')->label(__('common.fields.email'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('phone')->label(__('common.fields.phone'))->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('address')->label(__('common.fields.address'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('notes')->label(__('common.fields.notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
