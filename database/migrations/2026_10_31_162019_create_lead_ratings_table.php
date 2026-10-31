<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 30)->default('gray');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('lead_rating_id')
                ->nullable()
                ->after('status')
                ->constrained()
                ->nullOnDelete();
        });

        $ratingIds = $this->createDefaultRatings();
        $this->migrateLegacyRatings($ratingIds);

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['rating']);
            $table->dropColumn('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('rating', 20)->nullable()->after('status');
        });

        foreach (DB::table('lead_ratings')->pluck('name', 'id') as $id => $name) {
            DB::table('leads')->where('lead_rating_id', $id)->update([
                'rating' => Str::snake($name),
            ]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_rating_id');
            $table->index('rating');
        });

        Schema::dropIfExists('lead_ratings');
    }

    /**
     * @return array<string, int>
     */
    private function createDefaultRatings(): array
    {
        $defaults = [
            'hot' => [
                'name' => 'Hot',
                'color' => 'danger',
                'description' => 'Prospek dengan minat tinggi dan siap ditindaklanjuti.',
            ],
            'warm' => [
                'name' => 'Warm',
                'color' => 'warning',
                'description' => 'Prospek dengan minat sedang dan masih perlu pendekatan lanjutan.',
            ],
            'cold' => [
                'name' => 'Cold',
                'color' => 'info',
                'description' => 'Prospek dengan minat rendah, belum siap membeli.',
            ],
        ];

        $ids = [];

        foreach ($defaults as $key => $rating) {
            $ids[$key] = DB::table('lead_ratings')->insertGetId([
                ...$rating,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $ratingIds
     */
    private function migrateLegacyRatings(array $ratingIds): void
    {
        $legacyValues = DB::table('leads')
            ->whereNotNull('rating')
            ->distinct()
            ->pluck('rating');

        foreach ($legacyValues as $value) {
            $key = strtolower((string) $value);

            $ratingId = $ratingIds[$key] ?? DB::table('lead_ratings')->insertGetId([
                'name' => Str::headline((string) $value),
                'color' => 'gray',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $ratingIds[$key] = $ratingId;

            DB::table('leads')->where('rating', $value)->update([
                'lead_rating_id' => $ratingId,
            ]);
        }
    }
};
