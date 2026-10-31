<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'crm:mark-overdue-invoices';

    protected $description = 'Tandai invoice yang melewati jatuh tempo dan masih memiliki sisa tagihan';

    public function handle(): int
    {
        $updated = Invoice::query()
            ->whereIn('status', [
                InvoiceStatus::Sent->value,
                InvoiceStatus::PartiallyPaid->value,
            ])
            ->whereDate('due_date', '<', now())
            ->where('outstanding_balance', '>', 0)
            ->update(['status' => InvoiceStatus::Overdue->value]);

        $this->info("{$updated} invoice ditandai jatuh tempo.");

        return self::SUCCESS;
    }
}
