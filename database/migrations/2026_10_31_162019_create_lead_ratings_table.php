<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 30)->default('#9ca3af');
            $table->string('text_color', 7)->default('#ffffff');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('lead_rating_id')
                ->references('id')
                ->on('lead_ratings')
                ->nullOnDelete();
        });

        $this->createDefaultRatings();
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_rating_id']);
        });

        Schema::dropIfExists('lead_ratings');
    }

    private function createDefaultRatings(): void
    {
        $now = now();

        $ratings = [
            [
                'name' => 'Hot',
                'color' => '#ef4444',
                'text_color' => '#ffffff',
                'description' => 'Prospek dengan minat tinggi dan siap ditindaklanjuti.',
            ],
            [
                'name' => 'Warm',
                'color' => '#f59e0b',
                'text_color' => '#111827',
                'description' => 'Prospek dengan minat sedang dan masih perlu pendekatan lanjutan.',
            ],
            [
                'name' => 'Cold',
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
