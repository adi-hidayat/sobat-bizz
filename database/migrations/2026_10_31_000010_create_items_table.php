<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->morphs('itemable');

            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('sku', 50)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit', 50)->nullable();

            $table->decimal('quantity', 15, 2)->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
