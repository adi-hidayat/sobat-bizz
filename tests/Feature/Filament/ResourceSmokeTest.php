<?php

use App\Filament\Resources\Cities\Pages\ListCities;
use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\DeliveryOrders\Pages\ListDeliveryOrders;
use App\Filament\Resources\Districts\Pages\ListDistricts;
use App\Filament\Resources\IncomingPos\Pages\ListIncomingPos;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\LeadSources\Pages\ListLeadSources;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Proposals\Pages\CreateProposal;
use App\Filament\Resources\Proposals\Pages\ListProposals;
use App\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Resources\States\Pages\ListStates;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Taxes\Pages\ListTaxes;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Villages\Pages\ListVillages;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Tax;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole('Administrator');

    $this->actingAs($this->admin);
});

it('merender halaman daftar utama', function (string $page) {
    livewire($page)->assertOk();
})->with([
    ListLeads::class,
    ListCustomers::class,
    ListSuppliers::class,
    ListProducts::class,
    ListProposals::class,
    ListQuotations::class,
    ListIncomingPos::class,
    ListPurchaseOrders::class,
    ListDeliveryOrders::class,
    ListInvoices::class,
    ListPayments::class,
    ListUsers::class,
    ListLeadSources::class,
    ListTaxes::class,
    ListCountries::class,
    ListStates::class,
    ListCities::class,
    ListDistricts::class,
    ListVillages::class,
]);

it('merender form create lead', function () {
    livewire(CreateLead::class)->assertOk();
});

it('membuat proposal beserta item dari form', function () {
    $customer = Customer::factory()->create();
    $ppn = Tax::factory()->create([
        'name' => 'PPN 11%',
        'value' => 11,
        'is_active' => true,
        'is_default' => true,
    ]);
    $materai = Tax::factory()->fixed()->create([
        'name' => 'Materai',
        'value' => 25_000,
        'is_active' => true,
    ]);

    livewire(CreateProposal::class)
        ->fillForm([
            'customer_id' => $customer->getKey(),
            'proposal_date' => now()->toDateString(),
            'subject' => 'Implementasi CRM',
            'status' => 'draft',
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'Lisensi Tahunan',
                    'quantity' => 2,
                    'unit' => 'unit',
                    'unit_price' => 1_000_000,
                    'discount_amount' => 0,
                    'tax_ids' => [$ppn->getKey(), $materai->getKey()],
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $proposal = Proposal::query()->latest('id')->first();
    $item = $proposal?->items->first();

    expect($proposal)->not->toBeNull()
        ->and($proposal->items)->toHaveCount(1)
        ->and($item->taxes)->toHaveCount(2)
        ->and($item->taxes->pluck('tax_id')->all())->toBe([$ppn->getKey(), $materai->getKey()])
        ->and((float) $item->tax_rate)->toBe(11.0)
        ->and((float) $item->tax_amount)->toBe(245_000.0)
        ->and((float) $proposal->subtotal)->toBe(2_000_000.0)
        ->and((float) $proposal->grand_total)->toBe(2_245_000.0);
});

it('mengambil beberapa pajak item dari master data', function () {
    $ppn = Tax::factory()->create([
        'name' => 'PPN 12%',
        'value' => 12,
        'is_active' => true,
    ]);
    $pph = Tax::factory()->create([
        'name' => 'PPh 23',
        'value' => 2,
        'is_active' => true,
    ]);
    $proposal = Proposal::factory()->create();

    $item = $proposal->items()->create([
        'name' => 'Jasa Konsultasi',
        'quantity' => 1,
        'unit_price' => 1_000_000,
        'discount_amount' => 0,
        'tax_ids' => [$ppn->getKey(), $pph->getKey()],
    ]);

    expect($item->taxes)->toHaveCount(2)
        ->and((float) $item->tax_rate)->toBe(14.0)
        ->and((float) $item->tax_amount)->toBe(140_000.0)
        ->and((float) $item->total)->toBe(1_140_000.0);
});

it('mengonversi lead menjadi customer dari tabel', function () {
    $lead = Lead::factory()->qualified()->create();

    livewire(ListLeads::class)
        ->callAction(TestAction::make('convert')->table($lead));

    expect($lead->refresh()->status->value)->toBe('converted')
        ->and(Customer::query()->where('lead_id', $lead->getKey())->exists())->toBeTrue();
});
