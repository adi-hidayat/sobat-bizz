<?php

use App\Actions\ConvertProposalToQuotation;
use App\Models\Proposal;
use App\Services\Documents\DocumentPdfRenderer;

it('menghasilkan berkas pdf dari dokumen', function () {
    $proposal = Proposal::factory()->create();
    $proposal->items()->create([
        'name' => 'Lisensi CRM',
        'quantity' => 2,
        'unit' => 'unit',
        'unit_price' => 5_000_000,
        'tax_rate' => 11,
    ]);

    $renderer = app(DocumentPdfRenderer::class);

    $output = $renderer->render($proposal)->output();

    expect($output)->toStartWith('%PDF')
        ->and($renderer->filename($proposal))->toEndWith('.pdf');
});

it('mencatat aktivitas konversi pada log', function () {
    $proposal = Proposal::factory()->create();
    $proposal->items()->create([
        'name' => 'Konsultasi',
        'quantity' => 1,
        'unit_price' => 1_000_000,
        'tax_rate' => 0,
    ]);

    app(ConvertProposalToQuotation::class)->handle($proposal);

    $this->assertDatabaseHas('activity_logs', [
        'subject_type' => 'proposal',
        'subject_id' => $proposal->getKey(),
        'event' => 'converted',
    ]);
});
