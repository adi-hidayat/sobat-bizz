<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_pos', function (Blueprint $table) {
            $table->id();
            $table->string('incoming_po_number', 50)->unique();
            $table->string('customer_po_number', 100)->nullable();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_terms')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('draft');

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'po_date']);
            $table->index('customer_id');
            $table->index('customer_po_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_pos');
    }
};
