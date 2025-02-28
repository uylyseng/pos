<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('exchange_rate', 10, 4)->default(0);
            $table->decimal('amount_in_default_currency', 10, 2)->default(0);
            $table->enum('status', ['completed', 'pending', 'failed'])->default('completed');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
