<?php

namespace App\Actions;

use App\Enums\CustomerStatus;
use App\Enums\LeadStatus;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Lead;
use App\Services\Documents\DocumentLinker;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConvertLeadToCustomer
{
    public function __construct(private readonly DocumentLinker $linker) {}

    /**
     * @param  array<string, mixed>  $overrides
     */
    public function handle(Lead $lead, array $overrides = []): Customer
    {
        if (! $lead->isConvertible()) {
            throw new InvalidArgumentException(__('documents.errors.lead_not_convertible'));
        }

        return DB::transaction(function () use ($lead, $overrides): Customer {
            $billingLine = $overrides['billing_address'] ?? $lead->address;
            $shippingLine = $overrides['shipping_address'] ?? $lead->address;

            $companyName = $lead->company_name ?: ($lead->contact_person ?: $lead->lead_number);

            unset($overrides['billing_address'], $overrides['shipping_address']);

            $customer = Customer::query()->create(array_merge([
                'company_name' => $companyName,
                'customer_type' => $lead->lead_type->toCustomerType(),
                'email' => $lead->email,
                'phone' => $lead->phone,
                'status' => CustomerStatus::Active,
                'notes' => $lead->notes,
                'lead_id' => $lead->getKey(),
            ], $overrides));

            if (filled($billingLine)) {
                $customer->addresses()->create([
                    'type' => Address::TYPE_BILLING,
                    'address_line' => $billingLine,
                    'is_primary' => true,
                ]);
            }

            if (filled($shippingLine)) {
                $customer->addresses()->create([
                    'type' => Address::TYPE_SHIPPING,
                    'address_line' => $shippingLine,
                    'is_primary' => true,
                ]);
            }

            $contacts = $lead->contacts()->get();

            if ($contacts->isEmpty()) {
                if (filled($lead->contact_person)) {
                    $customer->contacts()->create([
                        'name' => $lead->contact_person,
                        'position' => $lead->job_title,
                        'email' => $lead->email,
                        'phone' => $lead->phone,
                        'is_primary' => true,
                    ]);
                }
            } else {
                $contacts->each(function ($contact) use ($customer): void {
                    $customer->contacts()->create($contact->only([
                        'name',
                        'position',
                        'email',
                        'phone',
                        'is_primary',
                        'notes',
                    ]));
                });
            }

            $lead->forceFill([
                'status' => LeadStatus::Converted,
                'converted_at' => now(),
            ])->save();

            $this->linker->link($lead, $customer);

            $lead->logActivity('converted', 'converted', [
                'customer_id' => $customer->getKey(),
                'customer_code' => $customer->customer_code,
            ]);

            return $customer;
        });
    }
}
