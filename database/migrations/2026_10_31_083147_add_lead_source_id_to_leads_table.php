<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Pemetaan nilai enum sumber lead lama ke nama sumber lead baru.
     *
     * @var array<string, string>
     */
    private const LEGACY_SOURCES = [
        'website' => 'Website',
        'referral' => 'Referral',
        'social_media' => 'Social Media',
        'advertisement' => 'Advertisement',
        'other' => 'Lainnya',
    ];

    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('lead_source_id')
                ->nullable()
                ->after('industry')
                ->constrained('lead_sources')
                ->nullOnDelete();
        });

        $now = now();

        DB::table('lead_sources')->insert(
            collect(self::LEGACY_SOURCES)
                ->values()
                ->map(fn (string $name, int $index): array => [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => ($index + 1) * 10,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all(),
        );

        $sourceIds = DB::table('lead_sources')->pluck('id', 'name');

        foreach (self::LEGACY_SOURCES as $legacy => $name) {
            DB::table('leads')
                ->where('source', $legacy)
                ->update([
                    'lead_source_id' => $sourceIds[$name] ?? null,
                    'updated_at' => $now,
                ]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('source', 30)->default('website')->after('industry');
        });

        $sourceNames = DB::table('lead_sources')->pluck('name', 'id');

        foreach (DB::table('leads')->whereNotNull('lead_source_id')->get(['id', 'lead_source_id']) as $lead) {
            $name = $sourceNames[$lead->lead_source_id] ?? 'Other';
            $legacy = array_search($name, self::LEGACY_SOURCES, true);

            DB::table('leads')->where('id', $lead->id)->update([
                'source' => $legacy !== false ? $legacy : Str::slug($name, '_'),
            ]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_source_id');
            $table->index('source');
        });
    }
};
