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
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained()->nullOnDelete();  // Added nullable()
            $table->decimal('price', 10, 2);
            $table->unique(['product_id', 'size_id'], 'unique_product_size');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                // Add indexes for common queries
            $table->index('product_id');                   // Product size lookup
            $table->index('size_id');                      // Size filtering
            $table->index(['deleted_at']);                 // Soft delete queries
            $table->index(['product_id', 'deleted_at']);   // Active product sizes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
