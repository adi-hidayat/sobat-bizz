<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * File ini memakai timestamp 000004 agar selalu jalan sebelum create_leads_table,
     * karena kolom leads.lead_rating_id memakai FK inline ke tabel di bawah ini.
     */
    public function up(): void
    {
        Schema::create('lead_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('color', 30)->default('#9ca3af');
            $table->string('text_color', 7)->default('#ffffff');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $this->createDefaultRatings();
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_ratings');
    }

    private function createDefaultRatings(): void
    {
        $now = now();

        $ratings = [
            [
                'name' => 'Hot',
                'sort_order' => 1,
                'color' => '#ef4444',
                'text_color' => '#ffffff',
                'description' => 'Prospek dengan minat tinggi dan siap ditindaklanjuti.',
            ],
            [
                'name' => 'Warm',
                'sort_order' => 2,
                'color' => '#f59e0b',
                'text_color' => '#111827',
                'description' => 'Prospek dengan minat sedang dan masih perlu pendekatan lanjutan.',
            ],
            [
                'name' => 'Cold',
                'sort_order' => 3,
                'color' => '#0ea5e9',
                'text_color' => '#ffffff',
                'description' => 'Prospek dengan minat rendah, belum siap membeli.',
            ],
        ];

        DB::table('lead_ratings')->insert(array_map(
            fn (array $rating): array => [
                ...$rating,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $ratings,
        ));
    }
};
