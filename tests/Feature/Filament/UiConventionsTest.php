<?php

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Proposals\Pages\CreateProposal;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('membuat setiap kolom tabel dapat disembunyikan', function () {
    expect(TextColumn::make('company_name')->isToggleable())->toBeTrue()
        ->and(TextColumn::make('status')->isToggleable())->toBeTrue()
        ->and(TextColumn::make('grand_total')->isToggleable())->toBeTrue();
});

it('menampilkan aksi lihat sebagai slide over', function () {
    expect(ViewAction::make()->isModalSlideOver())->toBeTrue();
});

it('mengelompokkan seluruh aksi baris dalam satu menu', function () {
    $lead = Lead::factory()->create();

    livewire(ListLeads::class)
        ->assertTableActionVisible('view', $lead)
        ->assertTableActionVisible('openDetail', $lead)
        ->assertTableActionVisible('edit', $lead);
});

it('membuka detail baris dalam slide over dengan tab', function () {
    $lead = Lead::factory()->create();

    livewire(ListLeads::class)
        ->mountTableAction('view', $lead->getKey())
        ->assertMountedActionModalSee(['Informasi Lead', 'Kontak & Alamat']);
});

it('memisahkan form dengan beberapa informasi menjadi tab', function () {
    livewire(CreateProposal::class)
        ->assertSee('Informasi')
        ->assertSee('Item')
        ->assertSee('Ringkasan');

    livewire(CreateLead::class)
        ->assertSee('Informasi')
        ->assertSee('Kontak & Alamat');
});

it('mengubah satuan item lewat popup suffix', function () {
    $component = livewire(CreateProposal::class)
        ->fillForm([
            'items' => [
                ['name' => 'Item uji', 'quantity' => 2, 'unit_price' => 1000],
            ],
        ]);

    $itemKey = array_key_first($component->get('data.items') ?? []);

    $component->callAction(
        TestAction::make('editUnit')
            ->schemaComponent("items.{$itemKey}.quantity"),
        ['unit' => 'box'],
    );

    expect($component->get("data.items.{$itemKey}.unit"))->toBe('box');
});

it('merender item sebagai satu tabel yang dapat dipakai ulang', function () {
    livewire(CreateProposal::class)
        ->assertSeeHtml('fi-fo-table-repeater')
        ->assertSee('Produk / Jasa')
        ->assertSee('Nama item')
        ->assertSee('Jumlah')
        ->assertSeeHtml('placeholder="Qty"')
        ->assertSee('Satuan');
});
