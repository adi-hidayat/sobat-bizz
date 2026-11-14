<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * File ini memakai timestamp 150609 agar selalu jalan sebelum
     * create_lead_activities_table, karena kolom
     * lead_activities.lead_activity_type_id memakai FK inline ke tabel di bawah ini.
     */
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
    }

    public function down(): void
    {
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
};
