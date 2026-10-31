<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);
});

it('switches the locale for a guest and stores it in the session', function (): void {
    $this->get(route('locale.switch', 'en'))
        ->assertRedirect();

    expect(session('locale'))->toBe('en');
});

it('persists the locale preference for an authenticated user', function (): void {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin)
        ->get(route('locale.switch', 'en'))
        ->assertRedirect();

    expect(session('locale'))->toBe('en')
        ->and($admin->fresh()->locale)->toBe('en');
});

it('renders the panel in the selected locale', function (): void {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dasbor');

    $this->get(route('locale.switch', 'en'))->assertRedirect();

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Dashboard');

    $this->get(route('locale.switch', 'id'))->assertRedirect();

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Dasbor');
});

it('renders resource labels in the selected locale', function (): void {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin)
        ->get('/admin/customers')
        ->assertOk()
        ->assertSee('Perusahaan')
        ->assertDontSee('Company name');

    $this->get(route('locale.switch', 'en'))->assertRedirect();

    $this->get('/admin/customers')
        ->assertOk()
        ->assertSee('Company')
        ->assertDontSee('Perusahaan');
});

it('renders the language switcher on the login page and the panel', function (): void {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Bahasa Indonesia')
        ->assertSee('English');

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Bahasa Indonesia')
        ->assertSee('English');
});

it('rejects unsupported locales', function (): void {
    $this->get(route('locale.switch', 'fr'))->assertNotFound();
});
