<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number', 50)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('proposal_date');
            $table->date('expiry_date')->nullable();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->text('scope_of_work')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('status', 30)->default('draft');
            $table->string('currency', 3)->default('IDR');

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'proposal_date']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
