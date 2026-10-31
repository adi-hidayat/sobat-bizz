<?php

namespace App\Filament\Resources\Proposals\Pages;

use App\Enums\ProposalStatus;
use App\Filament\Resources\Proposals\ProposalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProposal extends CreateRecord
{
    protected static string $resource = ProposalResource::class;

    /**
     * Mendukung pembuatan proposal langsung dari halaman customer.
     */
    public function mount(): void
    {
        parent::mount();

        $customerId = request()->integer('customer_id');

        if ($customerId > 0) {
            $this->form->fill([
                'customer_id' => $customerId,
                'proposal_date' => now()->toDateString(),
                'status' => ProposalStatus::Draft->value,
                'currency' => config('crm.currency'),
                'items' => [],
            ]);
        }
    }
}
