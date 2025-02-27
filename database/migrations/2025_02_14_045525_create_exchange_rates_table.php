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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 10, 4);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Currency relationships with proper nullable() for nullOnDelete
            $table->foreignId('from_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('to_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


            // Add indexes for common queries
            $table->index('is_active');                                           // Active rates
            $table->index(['from_currency_id', 'to_currency_id', 'is_active']);  // Currency pair lookup
            $table->index(['start_date', 'end_date']);                           // Date range queries
            $table->index(['deleted_at']);  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
