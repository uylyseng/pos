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
        Schema::create('product_toppings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('topping_id')->nullable()->constrained()->nullOnDelete();  // Added nullable()
            $table->decimal('price', 10, 2);
            $table->unique(['product_id', 'topping_id'], 'unique_product_topping');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


            // Add indexes for common queries
            $table->index('product_id');                   // Product topping lookup
            $table->index('topping_id');                   // Topping filtering
            $table->index(['deleted_at']);                 // Soft delete queries
            $table->index(['product_id', 'deleted_at']);   // Active product toppings
            $table->index(['created_at']);                 // Date range queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_toppings');
    }
};
