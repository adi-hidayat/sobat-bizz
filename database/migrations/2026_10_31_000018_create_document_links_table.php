<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_links', function (Blueprint $table) {
            $table->id();
            $table->morphs('source');
            $table->morphs('target');
            $table->timestamp('created_at')->nullable();

            $table->index(['source_type', 'source_id', 'target_type']);
            $table->index(['target_type', 'target_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_links');
    }
};
