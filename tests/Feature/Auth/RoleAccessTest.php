<?php

use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\PurchaseOrder;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);
});

function userWithRole(string $role): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

it('memberi akses penuh kepada administrator', function () {
    $admin = userWithRole('Administrator');
    $lead = Lead::factory()->create();

    expect($admin->can('viewAny', Lead::class))->toBeTrue()
        ->and($admin->can('delete', $lead))->toBeTrue()
        ->and($admin->can('create', Proposal::class))->toBeTrue();
});

it('membatasi sales executive tanpa hak hapus', function () {
    $sales = userWithRole('Sales Executive');
    $lead = Lead::factory()->create();

    expect($sales->can('viewAny', Lead::class))->toBeTrue()
        ->and($sales->can('create', Lead::class))->toBeTrue()
        ->and($sales->can('update', $lead))->toBeTrue()
        ->and($sales->can('delete', $lead))->toBeFalse();
});

it('menolak finance mengubah proposal', function () {
    $finance = userWithRole('Finance');

    expect($finance->can('viewAny', Proposal::class))->toBeTrue()
        ->and($finance->can('create', Proposal::class))->toBeFalse();
});

it('mengunci dokumen yang sudah terbit dari perubahan', function () {
    $admin = userWithRole('Administrator');

    $draft = Proposal::factory()->create();
    $sent = Proposal::factory()->sent()->create();

    expect($admin->can('update', $draft))->toBeTrue()
        ->and($admin->can('update', $sent))->toBeFalse()
        ->and($admin->can('delete', $sent))->toBeFalse();
});

it('menolak warehouse mengubah delivery order yang sudah terkirim', function () {
    $warehouse = userWithRole('Warehouse');

    $draft = DeliveryOrder::factory()->create();
    $delivered = DeliveryOrder::factory()->delivered()->create();

    expect($warehouse->can('update', $draft))->toBeTrue()
        ->and($warehouse->can('update', $delivered))->toBeFalse();
});

it('memberi purchasing akses ke purchase order', function () {
    $purchasing = userWithRole('Purchasing');

    expect($purchasing->can('create', PurchaseOrder::class))->toBeTrue()
        ->and($purchasing->can('create', IncomingPo::class))->toBeFalse();
});

it('membatasi pengelolaan data staff ke administrator', function () {
    $admin = userWithRole('Administrator');
    $sales = userWithRole('Sales Executive');

    expect($admin->can('viewAny', Staff::class))->toBeTrue()
        ->and($admin->can('create', Staff::class))->toBeTrue()
        ->and($sales->can('viewAny', Staff::class))->toBeTrue()
        ->and($sales->can('create', Staff::class))->toBeFalse();
});
