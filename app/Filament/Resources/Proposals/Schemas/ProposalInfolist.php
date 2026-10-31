<?php

namespace App\Filament\Resources\Proposals\Schemas;

use App\Filament\Schemas\ItemsInfolist;
use App\Filament\Support\RelatedDocuments;
use App\Support\Format;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProposalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('proposal')
                    ->tabs([
                        Tab::make(__('resources/proposal.tabs.summary'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('resources/proposal.sections.information'))
                                    ->schema([
                                        TextEntry::make('proposal_number')->label(__('common.fields.number')),
                                        TextEntry::make('customer.company_name')->label(__('common.fields.customer')),
                                        TextEntry::make('subject')->label(__('resources/proposal.fields.subject')),
                                        TextEntry::make('proposal_date')->label(__('common.fields.date'))->date('d M Y'),
                                        TextEntry::make('expiry_date')->label(__('common.fields.expiry_date'))->date('d M Y')->placeholder(__('common.placeholders.none')),
                                        TextEntry::make('status')->label(__('common.fields.status'))->badge(),
                                        TextEntry::make('currency')->label(__('common.fields.currency')),
                                        TextEntry::make('createdBy.name')->label(__('resources/proposal.fields.created_by'))->placeholder(__('common.placeholders.none')),
                                    ])
                                    ->columns(3),
                                Section::make(__('resources/proposal.sections.value_summary'))
                                    ->schema([
                                        TextEntry::make('subtotal')->label(__('common.fields.subtotal'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('discount_amount')->label(__('common.fields.discount_amount'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('tax_amount')->label(__('common.fields.tax'))->formatStateUsing(fn ($state): string => Format::money($state)),
                                        TextEntry::make('grand_total')
                                            ->label(__('common.fields.grand_total'))
                                            ->formatStateUsing(fn ($state): string => Format::money($state))
                                            ->weight('bold'),
                                    ])
                                    ->columns(4),
                            ]),

                        Tab::make(__('resources/proposal.tabs.items'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                ItemsInfolist::section(__('resources/proposal.sections.items')),
                            ]),

                        Tab::make(__('resources/proposal.tabs.documents'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('resources/proposal.sections.related_documents'))
                                    ->schema([
                                        TextEntry::make('source_documents')
                                            ->label(__('resources/proposal.fields.source_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::sources($record)),
                                        TextEntry::make('target_documents')
                                            ->label(__('resources/proposal.fields.target_documents'))
                                            ->state(fn ($record): string => RelatedDocuments::targets($record)),
                                    ])
                                    ->columns(2),
                                Section::make(__('resources/proposal.sections.description'))
                                    ->schema([
                                        TextEntry::make('description')->label(__('common.fields.description'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('scope_of_work')->label(__('resources/proposal.fields.scope_of_work_short'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('terms_and_conditions')->label(__('common.fields.terms_and_conditions'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                        TextEntry::make('notes')->label(__('resources/proposal.fields.internal_notes'))->placeholder(__('common.placeholders.none'))->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
