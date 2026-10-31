<?php

use App\Enums\TaxType;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\LeadSources\Pages\ListLeadSources;
use App\Filament\Resources\ProductCategories\Pages\ListProductCategories;
use App\Filament\Resources\Taxes\Pages\ListTaxes;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\ProductCategory;
use App\Models\Tax;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('menampilkan menu setup pada grup system', function () {
    $this->get('/admin')
        ->assertOk()
        ->assertSee('Sistem')
        ->assertSee('Setup');
});

it('menampilkan kategori produk pada halaman setup', function () {
    $this->get('/admin/setup/product-categories')
        ->assertOk()
        ->assertSee('Kategori Produk');
});

it('merender halaman master data setup', function (string $page) {
    livewire($page)->assertOk();
})->with([
    ListLeadSources::class,
    ListTaxes::class,
    ListProductCategories::class,
]);

it('membuka form sumber lead dan pajak dalam slide over', function (string $page, string $field) {
    livewire($page)
        ->mountAction('create')
        ->assertMountedActionModalSee([$field]);
})->with([
    [ListLeadSources::class, 'Nama sumber'],
    [ListTaxes::class, 'Nama pajak'],
]);

it('membuka form ubah sumber lead dalam slide over', function () {
    $source = LeadSource::factory()->create();

    livewire(ListLeadSources::class)
        ->mountTableAction('edit', $source)
        ->assertMountedActionModalSee(['Nama sumber']);
});

it('membuat kategori produk dari halaman setup', function () {
    livewire(ListProductCategories::class)
        ->callAction('create', [
            'name' => 'Aksesoris',
            'description' => 'Perlengkapan tambahan',
        ])
        ->assertHasNoActionErrors();

    expect(ProductCategory::query()->where('name', 'Aksesoris')->exists())->toBeTrue();
});

it('membuat sumber lead baru dari modal', function () {
    livewire(ListLeadSources::class)
        ->callAction('create', [
            'name' => 'Google Ads',
            'sort_order' => 5,
            'is_active' => true,
        ])
        ->assertHasNoActionErrors();

    expect(LeadSource::query()->where('name', 'Google Ads')->exists())->toBeTrue();
});

it('membuat pajak percentage dan menghitung nilainya', function () {
    livewire(ListTaxes::class)
        ->callAction('create', [
            'name' => 'PPN 12%',
            'code' => 'PPN12',
            'type' => TaxType::Percentage->value,
            'value' => 12,
            'is_withholding' => false,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 50,
        ])
        ->assertHasNoActionErrors();

    $tax = Tax::query()->where('code', 'PPN12')->first();

    expect($tax)->not->toBeNull()
        ->and($tax->type)->toBe(TaxType::Percentage)
        ->and($tax->amountFor(1_000_000.0))->toBe(120_000.0);
});

it('menghitung pajak fixed tanpa persentase', function () {
    $tax = Tax::factory()->fixed()->create(['value' => 25_000]);

    expect($tax->amountFor(1_000_000.0))->toBe(25_000.0);
});

it('memakai sumber lead dari master data pada form lead', function () {
    $source = LeadSource::factory()->create(['name' => 'Instagram']);

    livewire(CreateLead::class)
        ->fillForm([
            'company_name' => 'PT Media Sosial',
            'lead_source_id' => $source->getKey(),
            'status' => 'new',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Lead::query()->latest('id')->first()->leadSource?->is($source))->toBeTrue();
});
