<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->string('type', 20)->default('billing');
            $table->string('label')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address_line')->nullable();

            $table->foreignId('country_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('regions')->nullOnDelete();

            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
