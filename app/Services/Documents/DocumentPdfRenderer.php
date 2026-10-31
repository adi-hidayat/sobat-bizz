<?php

namespace App\Services\Documents;

use App\Enums\DocumentType;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Proposal;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Database\Eloquent\Model;

final class DocumentPdfRenderer
{
    public function render(Model $document): PdfDocument
    {
        return Pdf::loadView('pdf.document', $this->data($document))->setPaper('a4', 'portrait');
    }

    public function filename(Model $document): string
    {
        $number = $document->getAttribute($document->documentType()->numberAttribute());

        return str_replace(['/', '\\'], '-', (string) $number).'.pdf';
    }

    /**
     * @return array<string, mixed>
     */
    private function data(Model $document): array
    {
        /** @var DocumentType $type */
        $type = $document->documentType();

        return [
            'document' => $document,
            'type' => $type,
            'number' => $document->getAttribute($type->numberAttribute()),
            'party' => $this->party($document),
            'meta' => $this->meta($document),
            'items' => method_exists($document, 'items') ? $document->items()->with('taxes')->get() : collect(),
            'hasTotals' => in_array($type, [
                DocumentType::Proposal,
                DocumentType::Quotation,
                DocumentType::IncomingPo,
                DocumentType::PurchaseOrder,
                DocumentType::Invoice,
            ], true),
            'terms' => $document->getAttribute('terms_and_conditions'),
            'notes' => $document->getAttribute('notes') ?? $document->getAttribute('delivery_notes'),
        ];
    }

    /**
     * @return array{label: string, name: string, address: ?string, email: ?string, phone: ?string}|null
     */
    private function party(Model $document): ?array
    {
        $party = $document->getAttribute('supplier_id') !== null
            ? $document->getRelationValue('supplier')
            : $document->getRelationValue('customer');

        if ($party === null && method_exists($document, 'customer')) {
            $party = $document->customer;
        }

        if ($party === null) {
            return null;
        }

        $address = method_exists($party, 'addresses')
            ? ($party->addresses->firstWhere('type', 'billing') ?? $party->addresses->first())
            : null;

        return [
            'label' => $document->getAttribute('supplier_id') !== null
                ? __('documents.pdf.party.supplier')
                : __('documents.pdf.party.customer'),
            'name' => (string) $party->company_name,
            'address' => $address?->full_address,
            'email' => $party->email,
            'phone' => $party->phone,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function meta(Model $document): array
    {
        $meta = [];

        $date = match (true) {
            $document instanceof Proposal => $document->proposal_date,
            $document instanceof Quotation => $document->quotation_date,
            $document instanceof IncomingPo => $document->po_date,
            $document instanceof PurchaseOrder => $document->po_date,
            $document instanceof DeliveryOrder => $document->delivery_date,
            $document instanceof Invoice => $document->invoice_date,
            $document instanceof Payment => $document->payment_date,
            default => null,
        };

        if ($date !== null) {
            $meta[__('documents.pdf.meta.date')] = $date->translatedFormat('d F Y');
        }

        if ($document instanceof Proposal && $document->expiry_date !== null) {
            $meta[__('documents.pdf.meta.valid_until')] = $document->expiry_date->translatedFormat('d F Y');
        }

        if ($document instanceof Quotation && $document->valid_until !== null) {
            $meta[__('documents.pdf.meta.valid_until')] = $document->valid_until->translatedFormat('d F Y');
            $meta[__('documents.pdf.meta.version')] = (string) $document->version;
        }

        if ($document instanceof IncomingPo) {
            if (filled($document->customer_po_number)) {
                $meta[__('documents.pdf.meta.customer_po')] = (string) $document->customer_po_number;
            }

            if ($document->expected_delivery_date !== null) {
                $meta[__('documents.pdf.meta.expected_delivery')] = $document->expected_delivery_date->translatedFormat('d F Y');
            }
        }

        if ($document instanceof PurchaseOrder && $document->expected_delivery_date !== null) {
            $meta[__('documents.pdf.meta.expected_arrival')] = $document->expected_delivery_date->translatedFormat('d F Y');
        }

        if ($document instanceof DeliveryOrder) {
            $meta[__('documents.pdf.meta.method')] = (string) ($document->delivery_method ?? '-');
            $meta[__('documents.pdf.meta.received_by')] = (string) ($document->received_by ?? '-');
        }

        if ($document instanceof Invoice) {
            $meta[__('documents.pdf.meta.due_date')] = $document->due_date->translatedFormat('d F Y');
        }

        if ($document instanceof Payment) {
            $meta[__('documents.pdf.meta.method')] = $document->method->getLabel();
            $meta[__('documents.pdf.meta.reference')] = (string) ($document->reference ?? '-');
        }

        if (isset($document->status)) {
            $meta[__('documents.pdf.meta.status')] = $document->status->getLabel();
        }

        return $meta;
    }
}
