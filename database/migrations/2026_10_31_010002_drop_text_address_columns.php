<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['billing_address', 'shipping_address']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->text('address')->nullable();
        });
    }
};
