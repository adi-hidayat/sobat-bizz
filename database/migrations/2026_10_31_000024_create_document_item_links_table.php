<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_item_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('target_item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->timestamp('created_at')->nullable();

            $table->unique(['source_item_id', 'target_item_id']);
            $table->index('target_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_item_links');
    }
};
