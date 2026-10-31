<?php

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Enums\DeliveryOrderStatus;
use App\Enums\DocumentType;
use App\Enums\IncomingPoStatus;
use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Enums\PaymentMethod;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Enums\ProposalStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\QuotationStatus;
use App\Enums\SupplierStatus;
use App\Enums\TaxType;
use App\Filament\Support\NavigationGroup;
use Illuminate\Support\Str;

it('translates enum labels using the active locale', function (): void {
    app()->setLocale('id');

    expect(CustomerStatus::Active->getLabel())->toBe('Aktif')
        ->and(InvoiceStatus::PartiallyPaid->getLabel())->toBe('Dibayar sebagian')
        ->and(PurchaseOrderStatus::PartiallyReceived->getLabel())->toBe('Diterima sebagian')
        ->and(DocumentType::IncomingPo->getLabel())->toBe('Incoming PO')
        ->and(TaxType::Fixed->getLabel())->toBe('Nominal tetap');

    app()->setLocale('en');

    expect(CustomerStatus::Active->getLabel())->toBe('Active')
        ->and(InvoiceStatus::PartiallyPaid->getLabel())->toBe('Partially Paid')
        ->and(PurchaseOrderStatus::PartiallyReceived->getLabel())->toBe('Partially Received')
        ->and(DocumentType::Payment->getLabel())->toBe('Payment')
        ->and(TaxType::Fixed->getLabel())->toBe('Fixed amount');
});

it('has a translation for every enum label', function (): void {
    $enums = [
        CustomerStatus::class,
        CustomerType::class,
        DeliveryOrderStatus::class,
        DocumentType::class,
        IncomingPoStatus::class,
        InvoiceStatus::class,
        LeadStatus::class,
        PaymentMethod::class,
        ProductStatus::class,
        ProductType::class,
        ProposalStatus::class,
        PurchaseOrderStatus::class,
        QuotationStatus::class,
        SupplierStatus::class,
        TaxType::class,
        NavigationGroup::class,
    ];

    foreach (['id', 'en'] as $locale) {
        app()->setLocale($locale);

        foreach ($enums as $enum) {
            foreach ($enum::cases() as $case) {
                $key = match ($enum) {
                    DocumentType::class, TaxType::class, NavigationGroup::class => match ($enum) {
                        DocumentType::class => 'enums.document_type.'.$case->value,
                        TaxType::class => 'enums.tax_type.'.$case->value,
                        default => 'navigation.groups.'.$case->value,
                    },
                    default => 'enums.'.Str::snake(class_basename($enum)).'.'.Str::snake($case->name),
                };

                expect(__($key))
                    ->not->toBe($key)
                    ->and($case->getLabel())->not->toBe($key);
            }
        }
    }
});
