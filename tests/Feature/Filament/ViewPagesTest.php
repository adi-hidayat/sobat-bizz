<?php

use App\Actions\ConvertProposalToQuotation;
use App\Actions\ConvertQuotationToIncomingPo;
use App\Filament\Resources\DeliveryOrders\Pages\ViewDeliveryOrder;
use App\Filament\Resources\IncomingPos\Pages\ViewIncomingPo;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Proposals\Pages\ViewProposal;
use App\Filament\Resources\Quotations\Pages\ViewQuotation;
use App\Filament\Widgets\WelcomeWidget;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Pages\Dashboard;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('merender layout panel beserta sidebar navigasi', function () {
    $this->get('/admin')
        ->assertOk()
        ->assertSee('Penjualan')
        ->assertSee('Invoice')
        ->assertSee('Delivery Order')
        ->assertDontSee('dr5hn')
        ->assertDontSee('Keuangan')
        ->assertDontSee('Fulfillment');
});

it('merender dashboard', function () {
    livewire(Dashboard::class)
        ->assertOk()
        ->assertSee('Selamat Datang');
});

it('menampilkan widget selamat datang selebar penuh', function () {
    livewire(WelcomeWidget::class)
        ->assertOk()
        ->assertSee('Selamat Datang');

    expect((new WelcomeWidget)->getColumnSpan())->toBe('full');
});

it('merender halaman detail proposal beserta dokumen turunan', function () {
    $customer = Customer::factory()->create();
    $proposal = Proposal::factory()->create(['customer_id' => $customer->getKey()]);
    $proposal->items()->create([
        'name' => 'Implementasi',
        'quantity' => 1,
        'unit_price' => 1_000_000,
        'tax_rate' => 11,
    ]);

    app(ConvertProposalToQuotation::class)->handle($proposal);

    livewire(ViewProposal::class, ['record' => $proposal->getKey()])
        ->assertOk()
        ->assertSee('Implementasi')
        ->assertSee('Quotation');
});

it('merender halaman detail quotation dan incoming po', function () {
    $quotation = Quotation::factory()->create();
    $quotation->items()->create([
        'name' => 'Jasa',
        'quantity' => 2,
        'unit_price' => 500_000,
        'tax_rate' => 0,
    ]);

    $incomingPo = app(ConvertQuotationToIncomingPo::class)->handle($quotation);

    livewire(ViewQuotation::class, ['record' => $quotation->getKey()])->assertOk();
    livewire(ViewIncomingPo::class, ['record' => $incomingPo->getKey()])->assertOk();
});

it('merender halaman detail delivery order', function () {
    $deliveryOrder = DeliveryOrder::factory()->create();
    $deliveryOrder->items()->create([
        'name' => 'Server',
        'quantity' => 1,
        'unit_price' => 10_000_000,
        'tax_rate' => 0,
    ]);

    livewire(ViewDeliveryOrder::class, ['record' => $deliveryOrder->getKey()])
        ->assertOk()
        ->assertSee('Server');
});

it('merender halaman detail invoice dan mencatat pembayaran', function () {
    $invoice = Invoice::factory()->sent()->create();
    $invoice->items()->create([
        'name' => 'Lisensi',
        'quantity' => 1,
        'unit_price' => 2_000_000,
        'tax_rate' => 0,
    ]);

    livewire(ViewInvoice::class, ['record' => $invoice->getKey()])
        ->assertOk()
        ->callAction('recordPayment', ['amount' => 1_000_000])
        ->assertHasNoActionErrors();

    expect((float) $invoice->refresh()->amount_paid)->toBe(1_000_000.0);
});

it('mencatat pembayaran langsung dari tabel invoice', function () {
    $invoice = Invoice::factory()->sent()->create();
    $invoice->items()->create([
        'name' => 'Lisensi',
        'quantity' => 1,
        'unit_price' => 2_000_000,
        'tax_rate' => 0,
    ]);

    livewire(ListInvoices::class)
        ->callAction(
            TestAction::make('recordPayment')->table($invoice),
            ['amount' => 500_000],
        )
        ->assertHasNoActionErrors();

    expect((float) $invoice->refresh()->amount_paid)->toBe(500_000.0)
        ->and((float) $invoice->outstanding_balance)->toBe(1_500_000.0);
});

it('merender halaman detail payment', function () {
    $payment = Payment::factory()->create();

    livewire(ViewPayment::class, ['record' => $payment->getKey()])->assertOk();
});

it('menghasilkan response unduhan pdf dari halaman detail', function () {
    $proposal = Proposal::factory()->create();
    $proposal->items()->create([
        'name' => 'Konsultasi',
        'quantity' => 1,
        'unit_price' => 1_000_000,
        'tax_rate' => 0,
    ]);

    livewire(ViewProposal::class, ['record' => $proposal->getKey()])
        ->callAction('downloadPdf')
        ->assertFileDownloaded();
});
