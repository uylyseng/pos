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
            // Make order_id nullable to support nullOnDelete
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            // Payment method reference
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            
            // Currency reference
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('amount', 10, 2);
            $table->decimal('exchange_rate', 10, 4)->default(0);
            $table->decimal('amount_in_default_currency', 10, 2)->default(0);
            $table->enum('status', ['completed', 'pending', 'failed'])->default('completed');
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


            // Add indexes for common queries
            $table->index('order_id');                          // Order payment lookup
            $table->index('status');                            // Payment status filtering
            $table->index('created_at');                        // Date range queries
            $table->index(['order_id', 'status']);              // Order payment status
            $table->index(['payment_method_id', 'status']);     // Payment method analytics
            $table->index(['created_at', 'status']);            // Payment status by date
            $table->index(['deleted_at']);   
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
