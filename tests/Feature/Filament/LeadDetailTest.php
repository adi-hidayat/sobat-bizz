<?php

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('mengarahkan detail lead ke lead information', function () {
    $lead = Lead::factory()->create();

    $this->get("/admin/lead-detail?lead={$lead->getKey()}")
        ->assertRedirect("/admin/lead-detail/{$lead->getKey()}/information");
});

it('mengarahkan halaman detail lead lama ke cluster lead detail', function () {
    $lead = Lead::factory()->create();

    $this->get("/admin/leads/{$lead->getKey()}")
        ->assertRedirect("/admin/lead-detail/{$lead->getKey()}/information");
});

it('merender seluruh menu cluster lead detail', function (string $slug) {
    $lead = Lead::factory()->create();

    $this->get("/admin/lead-detail/{$lead->getKey()}/{$slug}")
        ->assertOk()
        ->assertSee('fi-page-has-sub-navigation', false)
        ->assertSee('Informasi')
        ->assertSee('Kontak')
        ->assertSee('Aktivitas');
})->with([
    'information',
    'contact',
    'activities',
]);

it('menampilkan data lead dan alamat pada menu informasi', function () {
    $lead = Lead::factory()->create([
        'company_name' => 'PT Uji Detail',
        'contact_person' => 'Sari Uji',
        'address' => 'Jl. Uji Alamat No. 7',
    ]);

    $this->get("/admin/lead-detail/{$lead->getKey()}/information")
        ->assertOk()
        ->assertSee('PT Uji Detail')
        ->assertSee('Sari Uji')
        ->assertSee($lead->lead_number)
        ->assertSee('Jl. Uji Alamat No. 7');
});

it('menampilkan kontak pada menu kontak', function () {
    $lead = Lead::factory()->create();

    $lead->contacts()->create([
        'name' => 'Budi Kontak',
        'email' => 'budi@example.test',
    ]);

    $this->get("/admin/lead-detail/{$lead->getKey()}/contact")
        ->assertOk()
        ->assertSee($lead->email)
        ->assertSee('Budi Kontak');
});

it('menampilkan aktivitas pada menu aktivitas', function () {
    $lead = Lead::factory()->create();

    LeadActivity::factory()->create([
        'lead_id' => $lead->getKey(),
        'subject' => 'Telepon perdana',
    ]);

    $this->get("/admin/lead-detail/{$lead->getKey()}/activities")
        ->assertOk()
        ->assertSee('Telepon perdana');
});
