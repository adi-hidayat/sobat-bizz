<?php

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\Pages\BoardLeads;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole('Administrator');

    $this->actingAs($this->admin);
});

it('merender board pipeline beserta kartu lead', function () {
    $lead = Lead::factory()->create([
        'status' => LeadStatus::New,
        'estimated_value' => 5_000_000,
    ]);

    livewire(BoardLeads::class)
        ->assertOk()
        ->assertSee($lead->display_name)
        ->assertSee('5.000.000');
});

it('memindahkan lead ke status lain dari board', function () {
    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Contacted->value)
        ->assertOk();

    expect($lead->refresh()->status)->toBe(LeadStatus::Contacted);
});

it('mewajibkan alasan ketika memindahkan lead ke lost', function () {
    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Lost->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::New)
        ->and($lead->lost_reason)->toBeNull();
});

it('menyimpan alasan ketika memindahkan lead ke tidak berkualitas', function () {
    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Unqualified->value, 'Budget belum tersedia');

    expect($lead->refresh()->status)->toBe(LeadStatus::Unqualified)
        ->and($lead->lost_reason)->toBe('Budget belum tersedia');
});

it('menolak memindahkan lead ke status terkonversi', function () {
    $lead = Lead::factory()->qualified()->create();

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Converted->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::Qualified);
});

it('menolak memindahkan lead yang sudah terkonversi', function () {
    $lead = Lead::factory()->converted()->create();

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Contacted->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::Converted);
});

it('mengosongkan alasan lost ketika lead kembali ke status aktif', function () {
    $lead = Lead::factory()->create([
        'status' => LeadStatus::Lost,
        'lost_reason' => 'Budget belum ada',
    ]);

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Contacted->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::Contacted)
        ->and($lead->lost_reason)->toBeNull();
});

it('menandai pengguna saat ini pada pilihan sales', function () {
    $other = User::factory()->create(['is_active' => true]);
    $other->assignRole('Administrator');

    $options = livewire(BoardLeads::class)->instance()->getUserOptions();

    expect($options[$this->admin->getKey()])
        ->toContain(__('resources/lead.board.you_option', ['name' => $this->admin->name]))
        ->toContain('<img')
        ->and($options[$other->getKey()])->toContain($other->name)
        ->toContain('<img');
});

it('menolak perpindahan oleh pengguna tanpa izin update lead', function () {
    $finance = User::factory()->create(['is_active' => true]);
    $finance->assignRole('Finance');

    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    $this->actingAs($finance);

    expect($finance->can('update', $lead))->toBeFalse();

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Contacted->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::New);
});

it('membedakan warna status tidak berkualitas dan hilang', function () {
    expect(LeadStatus::Unqualified->getColor())->not->toBe(LeadStatus::Lost->getColor());
});

it('menempatkan lead pada posisi drop di kolom tujuan', function () {
    $first = Lead::factory()->create(['status' => LeadStatus::New, 'board_position' => 1]);
    $second = Lead::factory()->create(['status' => LeadStatus::New, 'board_position' => 2]);
    $moving = Lead::factory()->create(['status' => LeadStatus::Qualified, 'board_position' => 1]);

    livewire(BoardLeads::class)
        ->call('moveLead', $moving->getKey(), LeadStatus::New->value, null, 1);

    expect($first->refresh()->board_position)->toBe(1)
        ->and($moving->refresh()->board_position)->toBe(2)
        ->and($second->refresh()->board_position)->toBe(3);
});

it('mengirim notifikasi urutan ketika hanya mengubah posisi', function () {
    Lead::factory()->create(['status' => LeadStatus::New, 'board_position' => 1]);
    $second = Lead::factory()->create(['status' => LeadStatus::New, 'board_position' => 2]);

    livewire(BoardLeads::class)
        ->call('moveLead', $second->getKey(), LeadStatus::New->value, null, 0)
        ->assertNotified(__('resources/lead.board.notifications.reordered', [
            'lead' => $second->display_name,
        ]));
});

it('menolak pengguna tanpa izin melihat daftar lead membuka board', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    livewire(BoardLeads::class)->assertForbidden();
});
