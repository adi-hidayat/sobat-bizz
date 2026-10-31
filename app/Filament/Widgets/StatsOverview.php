<?php

namespace App\Filament\Widgets;

use App\Enums\IncomingPoStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ProposalStatus;
use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Support\Format;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $outstanding = (float) Invoice::query()
            ->whereNotIn('status', [InvoiceStatus::Draft->value, InvoiceStatus::Cancelled->value])
            ->sum('outstanding_balance');

        $overdue = Invoice::query()
            ->where('outstanding_balance', '>', 0)
            ->whereNotIn('status', [InvoiceStatus::Draft->value, InvoiceStatus::Cancelled->value])
            ->whereDate('due_date', '<', now())
            ->count();

        return [
            Stat::make(__('widgets.stats.lead'), Lead::query()->count())
                ->description(__('widgets.stats.lead_description', [
                    'count' => Lead::query()->whereDate('follow_up_date', '<=', now())->whereNotIn('status', ['converted', 'lost'])->count(),
                ]))
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('info'),

            Stat::make(__('widgets.stats.customer'), Customer::query()->where('status', 'active')->count())
                ->description(__('widgets.stats.customer_description'))
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->color('success'),

            Stat::make(__('widgets.stats.proposal_active'), Proposal::query()
                ->whereIn('status', [ProposalStatus::Draft->value, ProposalStatus::Sent->value, ProposalStatus::UnderReview->value])
                ->count())
                ->description(__('widgets.stats.proposal_accepted', [
                    'count' => Proposal::query()->where('status', ProposalStatus::Accepted->value)->count(),
                ]))
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('warning'),

            Stat::make(__('widgets.stats.quotation_pending'), Quotation::query()
                ->whereIn('status', [QuotationStatus::Draft->value, QuotationStatus::Sent->value, QuotationStatus::Negotiation->value])
                ->count())
                ->description(__('widgets.stats.quotation_accepted', [
                    'count' => Quotation::query()->where('status', QuotationStatus::Accepted->value)->count(),
                ]))
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->color('warning'),

            Stat::make(__('widgets.stats.incoming_po'), IncomingPo::query()
                ->whereNotIn('status', [IncomingPoStatus::Draft->value, IncomingPoStatus::Cancelled->value])
                ->count())
                ->description(__('widgets.stats.incoming_po_fulfilled', [
                    'count' => IncomingPo::query()->where('status', IncomingPoStatus::Fulfilled->value)->count(),
                ]))
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('info'),

            Stat::make(__('widgets.stats.delivery_order_pending'), DeliveryOrder::query()
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count())
                ->description(__('widgets.stats.delivery_order_description'))
                ->icon(Heroicon::OutlinedTruck)
                ->color('warning'),

            Stat::make(__('widgets.stats.outstanding'), Format::money($outstanding))
                ->description(__('widgets.stats.overdue', ['count' => $overdue]))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color($outstanding > 0 ? 'danger' : 'success'),
        ];
    }
}
