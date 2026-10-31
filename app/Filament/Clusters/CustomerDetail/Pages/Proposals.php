<?php

namespace App\Filament\Clusters\CustomerDetail\Pages;

use App\Filament\Resources\Proposals\ProposalResource;
use App\Models\Proposal;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class Proposals extends CustomerScopedPage
{
    protected static ?string $slug = 'proposals';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('pages.customer_detail.proposals');
    }

    public function getTitle(): string
    {
        return __('pages.customer_detail.proposals');
    }

    protected function relationship(): string
    {
        return 'proposals';
    }

    protected function defaultSort(): string
    {
        return 'proposal_date';
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('proposal_number')->label(__('pages.customer_detail.columns.number'))->searchable(),
            TextColumn::make('subject')->label(__('pages.customer_detail.columns.subject'))->limit(40)->searchable(),
            TextColumn::make('proposal_date')->label(__('pages.customer_detail.columns.date'))->date('d M Y')->sortable(),
            TextColumn::make('expiry_date')->label(__('pages.customer_detail.columns.valid_until'))->date('d M Y'),
            TextColumn::make('status')->label(__('pages.customer_detail.columns.status'))->badge(),
            TextColumn::make('grand_total')->label(__('pages.customer_detail.columns.grand_total'))->money('IDR')->sortable(),
        ];
    }

    protected function recordUrl(Model $record): ?string
    {
        return $record instanceof Proposal
            ? ProposalResource::getUrl('view', ['record' => $record])
            : null;
    }
}
