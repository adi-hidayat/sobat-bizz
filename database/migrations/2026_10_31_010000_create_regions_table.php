<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('level', 20);
            $table->string('code', 20)->nullable();
            $table->string('name', 150);
            $table->string('postal_code', 10)->nullable();
            $table->string('source', 20)->default('dr5hn');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();

            $table->unique(['source', 'level', 'code']);
            $table->index(['parent_id', 'level']);
            $table->index(['country_id', 'level']);
            $table->index(['level', 'name']);
            $table->index('postal_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
