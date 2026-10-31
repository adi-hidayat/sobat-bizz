<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 50)->unique();
            $table->string('company_name');
            $table->string('customer_type', 30)->default('company');
            $table->string('npwp', 50)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->unsignedSmallInteger('payment_term_days')->default(30);
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_name');
            $table->index('status');
            $table->index('customer_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
