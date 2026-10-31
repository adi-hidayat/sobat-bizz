<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('merender seluruh halaman index melalui http', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/admin',
    '/admin/leads',
    '/admin/customers',
    '/admin/suppliers',
    '/admin/products',
    '/admin/setup/product-categories',
    '/admin/proposals',
    '/admin/quotations',
    '/admin/incoming-pos',
    '/admin/purchase-orders',
    '/admin/delivery-orders',
    '/admin/invoice/invoices',
    '/admin/invoice/payments',
    '/admin/activity-logs',
    '/admin/users',
]);

it('merender halaman create seluruh dokumen', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/admin/leads/create',
    '/admin/customers/create',
    '/admin/suppliers/create',
    '/admin/products/create',
    '/admin/proposals/create',
    '/admin/quotations/create',
    '/admin/incoming-pos/create',
    '/admin/purchase-orders/create',
    '/admin/delivery-orders/create',
    '/admin/invoice/invoices/create',
    '/admin/invoice/payments/create',
]);

it('merender halaman detail melalui http', function () {
    $customer = Customer::factory()->create();
    $lead = Lead::factory()->create();
    $proposal = Proposal::factory()->create(['customer_id' => $customer->getKey()]);
    $invoice = Invoice::factory()->create(['customer_id' => $customer->getKey()]);

    $this->get("/admin/leads/{$lead->getKey()}")->assertOk();
    $this->get("/admin/customers/{$customer->getKey()}")->assertOk();
    $this->get("/admin/proposals/{$proposal->getKey()}")->assertOk();
    $this->get("/admin/invoice/invoices/{$invoice->getKey()}")->assertOk();
});

it('mengarahkan root ke panel admin', function () {
    $this->get('/')->assertRedirect('/admin');
});

it('menempatkan invoice dan payments dalam satu cluster di grup penjualan', function () {
    $this->get('/admin/invoice')
        ->assertRedirect('/admin/invoice/invoices');

    $this->get('/admin/invoice/invoices')
        ->assertOk()
        ->assertSee('Pembayaran')
        ->assertSee('/admin/invoice/payments', false);
});

it('tidak lagi menampilkan invoice dan payments di sidebar utama', function () {
    $response = $this->get('/admin');

    $response->assertOk()
        ->assertSee('/admin/invoice', false)
        ->assertDontSee('href="'.url('/admin/invoices').'"', false)
        ->assertDontSee('href="'.url('/admin/payments').'"', false);
});
