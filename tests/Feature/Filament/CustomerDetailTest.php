<?php

use App\Filament\Clusters\CustomerDetail\Pages\DeliveryOrders;
use App\Filament\Clusters\CustomerDetail\Pages\Invoices;
use App\Filament\Clusters\CustomerDetail\Pages\Proposals;
use App\Filament\Clusters\CustomerDetailCluster;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('mengarahkan detail customer ke customer information', function () {
    $customer = Customer::factory()->create();

    $this->get("/admin/customer-detail?customer={$customer->getKey()}")
        ->assertRedirect("/admin/customer-detail/{$customer->getKey()}/information");
});

it('merender seluruh menu cluster customer detail', function (string $slug) {
    $customer = Customer::factory()->create();

    $this->get("/admin/customer-detail/{$customer->getKey()}/{$slug}")
        ->assertOk()
        ->assertSee('fi-page-has-sub-navigation', false)
        ->assertSee('Customer Information')
        ->assertSee('Proposal')
        ->assertSee('Quotation')
        ->assertSee('Incoming PO')
        ->assertSee('Delivery Order')
        ->assertSee('Invoice');
})->with([
    'information',
    'proposals',
    'quotations',
    'incoming-pos',
    'delivery-orders',
    'invoices',
]);

it('menampilkan tab detail, alamat dan contact pada customer information', function () {
    $customer = Customer::factory()->create();

    $customer->addresses()->create([
        'type' => 'billing',
        'label' => 'Kantor Pusat',
        'address_line' => 'Jl. Penagihan No. 1',
        'postal_code' => '12190',
        'is_primary' => true,
    ]);

    $customer->addresses()->create([
        'type' => 'shipping',
        'label' => 'Gudang',
        'address_line' => 'Jl. Pengiriman No. 2',
        'postal_code' => '17112',
        'is_primary' => true,
    ]);

    $customer->contacts()->create([
        'name' => 'Budi Santoso',
        'position' => 'Procurement',
        'email' => 'budi@example.test',
        'is_primary' => true,
    ]);

    $this->get("/admin/customer-detail/{$customer->getKey()}/information")
        ->assertOk()
        ->assertSee('Detail')
        ->assertSee('Alamat')
        ->assertSee('Contact')
        ->assertSee('Jl. Penagihan No. 1')
        ->assertSee('Jl. Pengiriman No. 2')
        ->assertSee('Budi Santoso');
});

it('hanya menampilkan proposal milik customer yang sedang dibuka', function () {
    $customer = Customer::factory()->create();
    $otherCustomer = Customer::factory()->create();

    $ownProposal = Proposal::factory()->create(['customer_id' => $customer->getKey()]);
    $foreignProposal = Proposal::factory()->create(['customer_id' => $otherCustomer->getKey()]);

    livewire(Proposals::class, ['customer' => (string) $customer->getKey()])
        ->assertCanSeeTableRecords([$ownProposal])
        ->assertCanNotSeeTableRecords([$foreignProposal]);
});

it('hanya menampilkan delivery order dan invoice milik customer', function () {
    $customer = Customer::factory()->create();
    $otherCustomer = Customer::factory()->create();

    $ownDeliveryOrder = DeliveryOrder::factory()->create(['customer_id' => $customer->getKey()]);
    $foreignDeliveryOrder = DeliveryOrder::factory()->create(['customer_id' => $otherCustomer->getKey()]);
    $ownInvoice = Invoice::factory()->create(['customer_id' => $customer->getKey()]);
    $foreignInvoice = Invoice::factory()->create(['customer_id' => $otherCustomer->getKey()]);

    livewire(DeliveryOrders::class, ['customer' => (string) $customer->getKey()])
        ->assertCanSeeTableRecords([$ownDeliveryOrder])
        ->assertCanNotSeeTableRecords([$foreignDeliveryOrder]);

    livewire(Invoices::class, ['customer' => (string) $customer->getKey()])
        ->assertCanSeeTableRecords([$ownInvoice])
        ->assertCanNotSeeTableRecords([$foreignInvoice]);
});

it('menyimpan kontak customer melalui tab Contact pada form customer', function () {
    $customer = Customer::factory()->create();

    livewire(EditCustomer::class, ['record' => $customer->getKey()])
        ->fillForm([
            'contacts' => [
                [
                    'name' => 'Siti Rahma',
                    'position' => 'Finance',
                    'email' => 'siti@example.test',
                    'is_primary' => true,
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($customer->contacts()->where('name', 'Siti Rahma')->exists())->toBeTrue();
});

it('menampilkan tautan detail customer pada tabel customer', function () {
    $customer = Customer::factory()->create();

    livewire(ListCustomers::class)
        ->assertTableActionHasUrl(
            'openDetail',
            CustomerDetailCluster::getUrl(['customer' => $customer->getKey()]),
            $customer,
        );
});
