<?php

use App\Actions\ConvertLeadToCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

use function Pest\Livewire\livewire;

/**
 * @return array{0: Region, 1: Region, 2: Region, 3: Region, 4: Region}
 */
function regionChain(): array
{
    $country = Region::factory()->country()->create(['code' => 'ID', 'name' => 'Indonesia']);
    $province = Region::factory()->withParent($country)->create(['code' => '32', 'name' => 'Jawa Barat']);
    $city = Region::factory()->withParent($province)->create(['code' => '32.73', 'name' => 'Kota Bandung']);
    $district = Region::factory()->withParent($city)->create(['code' => '32.73.01', 'name' => 'Sukasari']);
    $village = Region::factory()->withParent($district)->create([
        'code' => '32.73.01.1001',
        'name' => 'Sukarasa',
        'postal_code' => '40152',
    ]);

    return [$country, $province, $city, $district, $village];
}

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('membangun rantai nama wilayah lengkap', function () {
    [$country, $province, $city, $district, $village] = regionChain();

    expect($village->level)->toBe(Region::LEVEL_VILLAGE)
        ->and($village->fullName())->toBe('Indonesia, Jawa Barat, Kota Bandung, Sukasari, Sukarasa')
        ->and(Region::countries()->count())->toBe(1)
        ->and($city->parent->is($province))->toBeTrue()
        ->and($province->country->is($country))->toBeTrue();
});

it('menyimpan alamat customer beserta wilayah dan kode pos', function () {
    [$country, $province, $city, $district, $village] = regionChain();

    $customer = Customer::factory()->create();

    livewire(EditCustomer::class, ['record' => $customer->getKey()])
        ->fillForm([
            'addresses' => [
                [
                    'type' => 'billing',
                    'label' => 'Kantor Pusat',
                    'address_line' => 'Jl. Asia Afrika No. 1',
                    'country_id' => $country->getKey(),
                    'state_id' => $province->getKey(),
                    'city_id' => $city->getKey(),
                    'district_id' => $district->getKey(),
                    'village_id' => $village->getKey(),
                    'postal_code' => '40152',
                    'is_primary' => true,
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $address = $customer->addresses()->first();

    expect($address)->not->toBeNull()
        ->and($address->village_id)->toBe($village->getKey())
        ->and($address->postal_code)->toBe('40152')
        ->and($address->full_address)->toContain('Jl. Asia Afrika No. 1')
        ->and($address->full_address)->toContain('Sukarasa')
        ->and($address->full_address)->toContain('Kota Bandung')
        ->and($address->full_address)->toContain('Indonesia');
});

it('membuat alamat billing dan shipping saat lead dikonversi', function () {
    $lead = Lead::factory()->create(['address' => 'Jl. Merdeka No. 10']);

    $customer = app(ConvertLeadToCustomer::class)->handle($lead);

    expect($customer->addresses()->count())->toBe(2)
        ->and($customer->addresses()->where('type', 'billing')->value('address_line'))->toBe('Jl. Merdeka No. 10')
        ->and($customer->addresses()->where('type', 'shipping')->value('address_line'))->toBe('Jl. Merdeka No. 10');
});
