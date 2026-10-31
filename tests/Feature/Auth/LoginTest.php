<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Auth\Pages\Login;

use function Pest\Livewire\livewire;

it('memungkinkan administrator bawaan masuk ke panel', function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(AdminUserSeeder::class);

    livewire(Login::class)
        ->fillForm([
            'email' => 'admin@crm.test',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(auth()->check())->toBeTrue()
        ->and(auth()->user()->email)->toBe('admin@crm.test');
});

it('menolak pengguna nonaktif', function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(AdminUserSeeder::class);

    User::query()->where('email', 'admin@crm.test')->update(['is_active' => false]);

    livewire(Login::class)
        ->fillForm([
            'email' => 'admin@crm.test',
            'password' => 'password',
        ])
        ->call('authenticate');

    expect(auth()->check())->toBeFalse();
});
