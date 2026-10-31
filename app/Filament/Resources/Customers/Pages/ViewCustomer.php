<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Clusters\CustomerDetailCluster;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Proposals\ProposalResource;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('customerDetail')
                ->label(__('resources/customer.actions.customer_detail'))
                ->icon('heroicon-o-document-duplicate')
                ->color('success')
                ->url(fn (Customer $record): string => CustomerDetailCluster::getUrl([
                    'customer' => $record->getKey(),
                ])),
            Action::make('createProposal')
                ->label(__('resources/customer.actions.create_proposal'))
                ->icon('heroicon-o-document-plus')
                ->color('gray')
                ->visible(fn (): bool => auth()->user()->can('create_proposal'))
                ->url(fn (Customer $record): string => ProposalResource::getUrl('create', [
                    'customer_id' => $record->getKey(),
                ])),
        ];
    }
}
