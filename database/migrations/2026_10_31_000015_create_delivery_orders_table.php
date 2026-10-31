<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number', 50)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('delivery_date');
            $table->text('shipping_address')->nullable();
            $table->string('delivery_method')->nullable();
            $table->string('received_by')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('status', 30)->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'delivery_date']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
