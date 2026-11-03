<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name');
            $table->string('group', 30);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $this->seedDefaultTypes();

        Schema::table('lead_activities', function (Blueprint $table) {
            $table->foreignId('lead_activity_type_id')
                ->nullable()
                ->constrained('lead_activity_types')
                ->nullOnDelete();
        });

        $this->backfillActivityTypes();

        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->string('type', 20)->nullable();
        });

        foreach (DB::table('lead_activity_types')->pluck('key', 'id') as $id => $key) {
            DB::table('lead_activities')
                ->where('lead_activity_type_id', $id)
                ->update(['type' => $key]);
        }

        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropForeign(['lead_activity_type_id']);
            $table->dropColumn('lead_activity_type_id');
        });

        Schema::dropIfExists('lead_activity_types');
    }

    private function seedDefaultTypes(): void
    {
        $now = now();

        $types = [
            ['key' => 'call', 'name' => 'Panggilan Telepon', 'group' => 'communication', 'sort_order' => 1],
            ['key' => 'email', 'name' => 'Email', 'group' => 'communication', 'sort_order' => 2],
            ['key' => 'meeting', 'name' => 'Meeting', 'group' => 'meeting', 'sort_order' => 3],
            ['key' => 'quotation', 'name' => 'Penawaran', 'group' => 'sales', 'sort_order' => 4],
            ['key' => 'task', 'name' => 'Tugas', 'group' => 'task', 'sort_order' => 5],
            ['key' => 'note', 'name' => 'Catatan Internal', 'group' => 'internal', 'sort_order' => 6],
        ];

        DB::table('lead_activity_types')->insert(array_map(
            fn (array $type): array => [
                ...$type,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $types,
        ));
    }

    private function backfillActivityTypes(): void
    {
        $ids = DB::table('lead_activity_types')->pluck('id', 'key');

        foreach (['call', 'email', 'meeting', 'task', 'note'] as $key) {
            if (! isset($ids[$key])) {
                continue;
            }

            DB::table('lead_activities')
                ->where('type', $key)
                ->update(['lead_activity_type_id' => $ids[$key]]);
        }
    }
};
