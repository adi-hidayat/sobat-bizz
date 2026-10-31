<?php

namespace App\Filament\Resources\Proposals\Tables;

use App\Enums\ProposalStatus;
use App\Filament\Actions\DocumentActions;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Proposals\ProposalResource;
use App\Models\Proposal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProposalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proposal_number')->label(__('common.fields.number'))->searchable()->sortable(),
                TextColumn::make('customer.company_name')->label(__('common.fields.customer'))->searchable()->sortable(),
                TextColumn::make('subject')->label(__('resources/proposal.fields.subject'))->limit(40)->searchable(),
                TextColumn::make('proposal_date')->label(__('common.fields.date'))->date('d M Y')->sortable(),
                TextColumn::make('expiry_date')->label(__('common.fields.expiry_date'))->date('d M Y')->toggleable(),
                TextColumn::make('status')->label(__('common.fields.status'))->badge(),
                TextColumn::make('grand_total')->label(__('common.fields.grand_total'))->money('IDR')->sortable(),
                TextColumn::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('common.fields.status'))->options(ProposalStatus::class),
                SelectFilter::make('customer_id')->label(__('common.fields.customer'))->relationship('customer', 'company_name'),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Proposal $record): string => ProposalResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    DocumentActions::convertToQuotation(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('proposal_date', 'desc');
    }
}
