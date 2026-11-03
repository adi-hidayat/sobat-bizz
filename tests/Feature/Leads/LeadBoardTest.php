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

it('menyimpan alasan ketika memindahkan lead ke kalah', function () {
    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Lost->value, 'Budget belum tersedia');

    expect($lead->refresh()->status)->toBe(LeadStatus::Lost)
        ->and($lead->lost_reason)->toBe('Budget belum tersedia');
});

it('menolak memindahkan lead ke status menang', function () {
    $lead = Lead::factory()->qualified()->create();

    livewire(BoardLeads::class)
        ->call('moveLead', $lead->getKey(), LeadStatus::Converted->value);

    expect($lead->refresh()->status)->toBe(LeadStatus::Qualified);
});

it('menolak memindahkan lead yang sudah menang', function () {
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
        ->toContain(e(__('resources/lead.board.you_option', ['name' => $this->admin->name])))
        ->toContain('<img')
        ->and($options[$other->getKey()])->toContain(e($other->name))
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

it('menampilkan lima kolom status pada board', function () {
    $statuses = collect(livewire(BoardLeads::class)->instance()->getBoard()['columns'])
        ->pluck('status')
        ->map(fn (LeadStatus $status): string => $status->value)
        ->all();

    expect($statuses)->toBe([
        LeadStatus::New->value,
        LeadStatus::Contacted->value,
        LeadStatus::Qualified->value,
        LeadStatus::Converted->value,
        LeadStatus::Lost->value,
    ]);
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

it('menampilkan avatar tim sales pada kartu board', function () {
    $member = User::factory()->create(['name' => 'Sales Pendukung', 'is_active' => true]);
    $lead = Lead::factory()->create();
    $lead->salesTeam()->attach($member->getKey());

    livewire(BoardLeads::class)
        ->set('visibleCardFields', ['name', 'type', 'source', 'value', 'team'])
        ->assertSeeHtml('lead-board-team-avatar')
        ->assertSee('Sales Pendukung');
});

it('menampilkan informasi kartu default dan dapat diubah lewat aksi', function () {
    $lead = Lead::factory()->create(['estimated_value' => 7_500_000]);

    $component = livewire(BoardLeads::class)
        ->assertSet('visibleCardFields', ['name', 'type', 'source', 'value'])
        ->assertSee($lead->display_name)
        ->assertSee('7.500.000')
        ->assertDontSee('Sales penanggung jawab')
        ->assertDontSee('Tim sales');

    $component
        ->callAction('cardFields', ['fields' => ['name', 'owner']])
        ->assertSet('visibleCardFields', ['name', 'owner'])
        ->assertSee('Sales penanggung jawab')
        ->assertDontSee('Tim sales');
});

it('menyaring board berdasarkan owner atau anggota tim sales', function () {
    $member = User::factory()->create(['is_active' => true]);
    $other = User::factory()->create(['is_active' => true]);

    $ownerLead = Lead::factory()->assignedTo($other)->create(['company_name' => 'Owner Lead Co']);
    $teamLead = Lead::factory()->assignedTo($other)->create(['company_name' => 'Team Lead Co']);
    $teamLead->salesTeam()->attach($member->getKey());

    livewire(BoardLeads::class)
        ->set('assignedTo', (string) $member->getKey())
        ->assertSee('Team Lead Co')
        ->assertDontSee('Owner Lead Co');
});

it('membatasi 25 kartu per kolom dan memuat lagi saat diminta', function () {
    Lead::factory()->count(30)->create(['status' => LeadStatus::New]);

    $component = livewire(BoardLeads::class);

    $column = collect($component->instance()->getBoard()['columns'])
        ->first(fn (array $column): bool => $column['status'] === LeadStatus::New);

    expect($column['count'])->toBe(30)
        ->and($column['loaded'])->toBe(25)
        ->and($column['leads'])->toHaveCount(25)
        ->and($column['has_more'])->toBeTrue();

    $component->call('loadMoreLeads', LeadStatus::New->value);

    $column = collect($component->instance()->getBoard()['columns'])
        ->first(fn (array $column): bool => $column['status'] === LeadStatus::New);

    expect($column['loaded'])->toBe(30)
        ->and($column['leads'])->toHaveCount(30)
        ->and($column['has_more'])->toBeFalse();
});

it('menolak pengguna tanpa izin melihat daftar lead membuka board', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    livewire(BoardLeads::class)->assertForbidden();
});
