<?php

use App\Actions\ConvertLeadToCustomer;
use App\Enums\CustomerType;
use App\Enums\LeadActivityType;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Resources\Leads\RelationManagers\ActivitiesRelationManager;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadRating;
use App\Models\LeadSource;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $this->actingAs($admin);
});

it('membuat lead perorangan tanpa nama perusahaan', function () {
    $source = LeadSource::factory()->create();
    $rating = LeadRating::query()->firstOrCreate(
        ['name' => 'Hot'],
        ['color' => 'danger'],
    );

    livewire(CreateLead::class)
        ->fillForm([
            'lead_type' => LeadType::Individual->value,
            'contact_person' => 'Rina Wijaya',
            'email' => 'rina@example.com',
            'phone' => '081234567890',
            'lead_source_id' => $source->getKey(),
            'status' => LeadStatus::New->value,
            'lead_rating_id' => $rating->getKey(),
            'estimated_value' => 5_000_000,
            'expected_close_date' => now()->addDays(10)->toDateString(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->latest('id')->first();

    expect($lead)->not->toBeNull()
        ->and($lead->lead_type)->toBe(LeadType::Individual)
        ->and($lead->company_name)->toBeNull()
        ->and($lead->contact_person)->toBe('Rina Wijaya')
        ->and($lead->display_name)->toBe('Rina Wijaya')
        ->and($lead->rating?->name)->toBe('Hot')
        ->and((float) $lead->estimated_value)->toBe(5_000_000.0)
        ->and($lead->expected_close_date)->not->toBeNull();
});

it('mewajibkan nama perusahaan untuk lead bertipe perusahaan', function () {
    $source = LeadSource::factory()->create();

    livewire(CreateLead::class)
        ->fillForm([
            'lead_type' => LeadType::Company->value,
            'company_name' => '',
            'lead_source_id' => $source->getKey(),
            'status' => LeadStatus::New->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['company_name' => 'required']);
});

it('mewajibkan alasan ketika lead lost atau tidak berkualitas', function () {
    $source = LeadSource::factory()->create();

    livewire(CreateLead::class)
        ->fillForm([
            'lead_type' => LeadType::Company->value,
            'company_name' => 'PT Contoh',
            'lead_source_id' => $source->getKey(),
            'status' => LeadStatus::Lost->value,
            'lost_reason' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['lost_reason' => 'required']);
});

it('menampilkan lead perorangan dengan nama orang pada tabel', function () {
    $lead = Lead::factory()->individual()->create([
        'contact_person' => 'Rina Wijaya',
        'company_name' => null,
    ]);

    livewire(ListLeads::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$lead])
        ->assertSee('Rina Wijaya')
        ->searchTable('Rina Wijaya')
        ->assertCanSeeTableRecords([$lead]);
});

it('memperbarui waktu terakhir dihubungi dari aktivitas kontak', function () {
    $lead = Lead::factory()->create();
    $occurredAt = now()->subHour();

    LeadActivity::query()->create([
        'lead_id' => $lead->getKey(),
        'type' => LeadActivityType::Call,
        'subject' => 'Follow up telepon',
        'occurred_at' => $occurredAt,
    ]);

    expect($lead->refresh()->last_contacted_at?->toDateTimeString())->toBe($occurredAt->toDateTimeString());

    LeadActivity::query()->create([
        'lead_id' => $lead->getKey(),
        'type' => LeadActivityType::Task,
        'subject' => 'Kirim penawaran',
        'occurred_at' => now(),
        'due_at' => now()->addDay(),
    ]);

    expect($lead->refresh()->last_contacted_at?->toDateTimeString())->toBe($occurredAt->toDateTimeString());
});

it('menampilkan aktivitas pada halaman lead', function () {
    $lead = Lead::factory()->create();
    $activity = LeadActivity::factory()->create([
        'lead_id' => $lead->getKey(),
        'subject' => 'Telepon perdana',
    ]);

    livewire(ActivitiesRelationManager::class, [
        'ownerRecord' => $lead,
        'pageClass' => ViewLead::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$activity]);
});

it('menyimpan owner dan tim sales tambahan pada lead', function () {
    $owner = User::factory()->create(['is_active' => true]);
    $member = User::factory()->create(['is_active' => true]);
    $source = LeadSource::factory()->create();

    livewire(CreateLead::class)
        ->fillForm([
            'lead_type' => LeadType::Company->value,
            'company_name' => 'PT Tim Sales',
            'lead_source_id' => $source->getKey(),
            'status' => LeadStatus::New->value,
            'assigned_to' => $owner->getKey(),
            'salesTeam' => [$member->getKey()],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->where('company_name', 'PT Tim Sales')->firstOrFail();

    expect($lead->assigned_to)->toBe($owner->getKey())
        ->and($lead->salesTeam->pluck('id')->all())->toBe([$member->getKey()]);
});

it('mengeluarkan owner dari daftar tim sales', function () {
    $owner = User::factory()->create(['is_active' => true]);
    $lead = Lead::factory()->assignedTo($owner)->create();
    $lead->salesTeam()->attach($owner->getKey());

    livewire(EditLead::class, ['record' => $lead->getKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($lead->salesTeam()->count())->toBe(0);
});

it('menyaring lead berdasarkan sales yang dipilih', function () {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('Administrator');

    $other = User::factory()->create(['is_active' => true]);
    $other->assignRole('Administrator');

    $mine = Lead::factory()->assignedTo($admin)->create();
    $theirs = Lead::factory()->assignedTo($other)->create();
    $teamOnly = Lead::factory()->assignedTo($other)->create();
    $teamOnly->salesTeam()->attach($admin->getKey());

    livewire(ListLeads::class)
        ->filterTable('assigned_to', ['value' => $admin->getKey()])
        ->assertCanSeeTableRecords([$mine, $teamOnly])
        ->assertCanNotSeeTableRecords([$theirs]);
});

it('mengonversi lead perorangan menjadi customer perorangan beserta kontak utama', function () {
    $lead = Lead::factory()->individual()->qualified()->create([
        'company_name' => null,
        'contact_person' => 'Rina Wijaya',
        'job_title' => 'Owner',
        'email' => 'rina@example.com',
        'phone' => '081234567890',
    ]);

    $customer = app(ConvertLeadToCustomer::class)->handle($lead);

    $contact = $customer->contacts()->first();

    expect($customer->customer_type)->toBe(CustomerType::Individual)
        ->and($customer->company_name)->toBe('Rina Wijaya')
        ->and($contact)->not->toBeNull()
        ->and($contact->name)->toBe('Rina Wijaya')
        ->and($contact->position)->toBe('Owner')
        ->and($contact->is_primary)->toBeTrue()
        ->and($lead->refresh()->status)->toBe(LeadStatus::Converted);
});
