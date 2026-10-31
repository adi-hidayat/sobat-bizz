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
            $table->string('lead_type', 20)->default('company')->after('lead_number');
            $table->string('company_name')->nullable()->change();
            $table->string('job_title')->nullable()->after('contact_person');
            $table->string('rating', 20)->nullable()->after('status');
            $table->decimal('estimated_value', 18, 2)->default(0)->after('rating');
            $table->date('expected_close_date')->nullable()->after('estimated_value');
            $table->text('lost_reason')->nullable()->after('notes');
            $table->timestamp('last_contacted_at')->nullable()->after('lost_reason');

            $table->index('email');
            $table->index('phone');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('leads')
            ->whereNull('company_name')
            ->update(['company_name' => DB::raw('coalesce(contact_person, lead_number)')]);

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['rating']);

            $table->dropColumn([
                'lead_type',
                'job_title',
                'rating',
                'estimated_value',
                'expected_close_date',
                'lost_reason',
                'last_contacted_at',
            ]);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('company_name')->nullable(false)->change();
        });
    }
};
