<?php

use App\Filament\Resources\Staff\Pages\CreateStaff;
use App\Filament\Resources\Staff\Pages\EditStaff;
use App\Filament\Resources\Staff\Pages\ListStaff;
use App\Filament\Resources\Staff\Pages\ViewStaff;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole('Administrator');

    $this->actingAs($this->admin);
});

it('membuat staff beserta akun pengguna dari form', function () {
    $department = Department::factory()->create(['name' => 'Sales']);
    $jobTitle = JobTitle::factory()->create(['name' => 'Sales Executive']);

    livewire(CreateStaff::class)
        ->fillForm([
            'employee_number' => 'EMP-0100',
            'department_id' => $department->getKey(),
            'joined_at' => now()->toDateString(),
            'user' => [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'job_title_id' => $jobTitle->getKey(),
                'phone' => '08123456789',
                'password' => 'secret-password',
                'is_active' => true,
                'roles' => [Role::findByName('Sales Executive')->getKey()],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $staff = Staff::query()->where('employee_number', 'EMP-0100')->first();

    expect($staff)->not->toBeNull()
        ->and($staff->department->name)->toBe('Sales')
        ->and($staff->user)->not->toBeNull()
        ->and($staff->user->name)->toBe('Budi Santoso')
        ->and($staff->user->email)->toBe('budi@example.com')
        ->and($staff->user->jobTitle->name)->toBe('Sales Executive')
        ->and($staff->user->hasRole('Sales Executive'))->toBeTrue()
        ->and(Hash::check('secret-password', $staff->user->password))->toBeTrue();
});

it('memperbarui data staff tanpa mengubah password yang dikosongkan', function () {
    $department = Department::factory()->create(['name' => 'Finance']);
    $staff = Staff::factory()->create(['employee_number' => 'EMP-0200']);
    $originalPassword = $staff->user->password;

    livewire(EditStaff::class, ['record' => $staff->getKey()])
        ->fillForm([
            'department_id' => $department->getKey(),
            'user' => [
                'name' => 'Nama Baru',
                'password' => '',
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $staff->refresh();

    expect($staff->department->name)->toBe('Finance')
        ->and($staff->user->name)->toBe('Nama Baru')
        ->and($staff->user->password)->toBe($originalPassword);
});

it('mengubah password akun ketika diisi pada form edit', function () {
    $staff = Staff::factory()->create(['employee_number' => 'EMP-0300']);

    livewire(EditStaff::class, ['record' => $staff->getKey()])
        ->fillForm([
            'user' => [
                'password' => 'password-baru',
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('password-baru', $staff->user->refresh()->password))->toBeTrue();
});

it('menampilkan daftar staff beserta data penggunanya', function () {
    $staff = Staff::factory()->create(['employee_number' => 'EMP-0400']);

    livewire(ListStaff::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$staff])
        ->assertSee($staff->user->name);
});

it('merender halaman create dan detail staff', function () {
    $staff = Staff::factory()->create(['employee_number' => 'EMP-0500']);

    livewire(CreateStaff::class)->assertOk();

    livewire(ViewStaff::class, ['record' => $staff->getKey()])
        ->assertOk()
        ->assertSee('EMP-0500')
        ->assertSee($staff->user->name);
});

it('menghapus akun pengguna saat data staff dihapus', function () {
    $staff = Staff::factory()->create();
    $userId = $staff->user_id;

    $staff->delete();

    expect(User::query()->find($userId))->toBeNull();
});

it('melarang staff menghapus datanya sendiri', function () {
    $ownStaff = Staff::factory()->create(['user_id' => $this->admin->getKey()]);
    $otherStaff = Staff::factory()->create();

    expect($this->admin->can('delete', $ownStaff))->toBeFalse()
        ->and($this->admin->can('delete', $otherStaff))->toBeTrue()
        ->and($this->admin->can('deleteAny', Staff::class))->toBeFalse();
});

it('menolak pengguna tanpa permission melihat data staff', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    livewire(ListStaff::class)->assertForbidden();
});
