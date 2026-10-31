<?php

use App\Filament\Resources\Cities\Pages\ListCities;
use App\Filament\Resources\Countries\CountryResource;
use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Filament\Resources\Districts\Pages\ListDistricts;
use App\Filament\Resources\States\Pages\ListStates;
use App\Filament\Resources\States\StateResource;
use App\Filament\Resources\Villages\Pages\ListVillages;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('merender halaman master data alamat', function (string $page) {
    livewire($page)->assertOk();
})->with([
    ListCountries::class,
    ListStates::class,
    ListCities::class,
    ListDistricts::class,
    ListVillages::class,
]);

it('menampilkan grup address pada sub navigasi setup', function () {
    livewire(ListCountries::class)
        ->assertOk()
        ->assertSee('Alamat')
        ->assertSee('Negara')
        ->assertSee('Provinsi');
});

it('membuka form negara dalam slide over', function () {
    livewire(ListCountries::class)
        ->mountAction('create')
        ->assertMountedActionModalSee(['Nama negara']);
});

it('membuka form ubah kota dalam slide over', function () {
    $city = Region::factory()->create();

    livewire(ListCities::class)
        ->mountTableAction('edit', $city)
        ->assertMountedActionModalSee(['Nama kota / kabupaten']);
});

it('membuat negara baru pada level country dari modal', function () {
    livewire(ListCountries::class)
        ->callAction('create', [
            'name' => 'Malaysia',
            'code' => 'MY',
        ])
        ->assertHasNoActionErrors();

    $country = Region::query()->where('name', 'Malaysia')->first();

    expect($country)->not->toBeNull()
        ->and($country->level)->toBe(Region::LEVEL_COUNTRY)
        ->and($country->source)->toBe('manual')
        ->and($country->parent_id)->toBeNull()
        ->and($country->country_id)->toBeNull();
});

it('membuat provinsi dengan induk negara dari modal', function () {
    $country = Region::factory()->country()->create();

    livewire(ListStates::class)
        ->callAction('create', [
            'name' => 'Jawa Tengah',
            'code' => '33',
            'country_id' => $country->getKey(),
        ])
        ->assertHasNoActionErrors();

    $state = Region::query()->where('name', 'Jawa Tengah')->first();

    expect($state)->not->toBeNull()
        ->and($state->level)->toBe(Region::LEVEL_STATE)
        ->and($state->parent_id)->toBe($country->getKey())
        ->and($state->country_id)->toBe($country->getKey())
        ->and($state->source)->toBe($country->source);
});

it('membuat kota dengan induk provinsi dan negara otomatis dari modal', function () {
    $country = Region::factory()->country()->create();
    $state = Region::factory()->withParent($country)->create();

    livewire(ListCities::class)
        ->callAction('create', [
            'name' => 'Kota Contoh',
            'code' => '3374',
            'country_id' => $country->getKey(),
            'parent_id' => $state->getKey(),
        ])
        ->assertHasNoActionErrors();

    $city = Region::query()->where('name', 'Kota Contoh')->first();

    expect($city)->not->toBeNull()
        ->and($city->level)->toBe(Region::LEVEL_CITY)
        ->and($city->parent_id)->toBe($state->getKey())
        ->and($city->country_id)->toBe($country->getKey())
        ->and($city->source)->toBe($country->source);
});

it('mengubah kota dari modal tabel', function () {
    $country = Region::factory()->country()->create();
    $state = Region::factory()->withParent($country)->create();
    $newState = Region::factory()->withParent($country)->create();
    $city = Region::factory()->withParent($state)->create();

    livewire(ListCities::class)
        ->callAction(
            TestAction::make('edit')->table($city),
            [
                'name' => 'Kota Ubah',
                'code' => '9999',
                'country_id' => $country->getKey(),
                'parent_id' => $newState->getKey(),
            ],
        )
        ->assertHasNoActionErrors();

    expect($city->refresh())
        ->name->toBe('Kota Ubah')
        ->parent_id->toBe($newState->getKey())
        ->country_id->toBe($country->getKey())
        ->source->toBe($country->source);
});

it('membatasi setiap resource sesuai levelnya', function () {
    $country = Region::factory()->country()->create();
    $state = Region::factory()->withParent($country)->create();

    expect(CountryResource::getEloquentQuery()->whereKey($state->getKey())->exists())->toBeFalse()
        ->and(StateResource::getEloquentQuery()->whereKey($country->getKey())->exists())->toBeFalse()
        ->and(StateResource::getEloquentQuery()->whereKey($state->getKey())->exists())->toBeTrue();
});
