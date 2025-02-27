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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_km');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->boolean('has_sizes')->default(false);
            $table->boolean('has_toppings')->default(false);
            $table->boolean('is_stock')->default(false);
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Add indexes for common queries
            $table->index('name_km');                    // Search by Khmer name
            $table->index('name_en');                    // Search by English name
            $table->index('is_active');                  // Filter active products
            $table->index(['category_id', 'is_active']); // Category listing with active status
            
            // Index for stock management
            $table->index(['is_stock', 'quantity', 'low_stock_threshold']); 
            
            // Index for product features
            $table->index(['has_sizes', 'has_toppings']); 
            
            // Composite index for sorting by creation date and status
            $table->index(['created_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
