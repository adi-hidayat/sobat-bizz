<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumLabel;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Proposal;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Supplier;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Model;

enum DocumentType: string implements HasLabel
{
    use HasEnumLabel;

    case Lead = 'lead';
    case Customer = 'customer';
    case Supplier = 'supplier';
    case Proposal = 'proposal';
    case Quotation = 'quotation';
    case IncomingPo = 'incoming_po';
    case PurchaseOrder = 'purchase_order';
    case DeliveryOrder = 'delivery_order';
    case Invoice = 'invoice';
    case Payment = 'payment';

    /**
     * @return class-string<Model>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Lead => Lead::class,
            self::Customer => Customer::class,
            self::Supplier => Supplier::class,
            self::Proposal => Proposal::class,
            self::Quotation => Quotation::class,
            self::IncomingPo => IncomingPo::class,
            self::PurchaseOrder => PurchaseOrder::class,
            self::DeliveryOrder => DeliveryOrder::class,
            self::Invoice => Invoice::class,
            self::Payment => Payment::class,
        };
    }

    /**
     * Kolom yang menyimpan nomor dokumen pada model terkait.
     */
    public function numberAttribute(): string
    {
        return match ($this) {
            self::Customer => 'customer_code',
            self::Supplier => 'supplier_code',
            self::Lead => 'lead_number',
            self::Proposal => 'proposal_number',
            self::Quotation => 'quotation_number',
            self::IncomingPo => 'incoming_po_number',
            self::PurchaseOrder => 'po_number',
            self::DeliveryOrder => 'do_number',
            self::Invoice => 'invoice_number',
            self::Payment => 'payment_number',
        };
    }

    public function prefix(): string
    {
        return (string) config("crm.numbering.{$this->value}.prefix", strtoupper(substr($this->value, 0, 3)));
    }

    public function getLabel(): string
    {
        return __('enums.document_type.'.$this->value);
    }

    public function pattern(): string
    {
        return (string) config("crm.numbering.{$this->value}.pattern", '{prefix}/{year}/{sequence:4}');
    }
}
