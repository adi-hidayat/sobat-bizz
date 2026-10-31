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
                                Section::make(__('resources/lead.sections.information'))
                                    ->schema([
                                        TextEntry::make('lead_number')->label(__('resources/lead.fields.lead_number')),
                                        TextEntry::make('company_name')->label(__('common.fields.company_name')),
                                        TextEntry::make('contact_person')->label(__('resources/lead.fields.contact_person')),
                                        TextEntry::make('industry')->label(__('common.fields.industry')),
                                        TextEntry::make('leadSource.name')->label(__('resources/lead.fields.source'))->badge(),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('assignedSalesPerson.name')->label(__('resources/lead.fields.sales')),
                                        TextEntry::make('follow_up_date')->label(__('resources/lead.fields.follow_up'))->date('d M Y'),
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
