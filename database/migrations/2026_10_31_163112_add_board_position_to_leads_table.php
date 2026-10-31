<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('board_position')->default(0)->after('lead_rating_id');
            $table->index(['status', 'board_position']);
        });

        $positions = [];

        DB::table('leads')
            ->select('id', 'status')
            ->orderBy('status')
            ->orderByRaw('follow_up_date is null')
            ->orderBy('follow_up_date')
            ->orderByDesc('created_at')
            ->orderBy('id')
            ->get()
            ->each(function (object $lead) use (&$positions): void {
                $positions[$lead->status] = ($positions[$lead->status] ?? 0) + 1;

                DB::table('leads')->where('id', $lead->id)->update([
                    'board_position' => $positions[$lead->status],
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status', 'board_position']);
            $table->dropColumn('board_position');
        });
    }
};
