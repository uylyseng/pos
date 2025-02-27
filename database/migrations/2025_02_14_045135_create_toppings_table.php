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
        Schema::create('toppings', function (Blueprint $table) {
            $table->id();
            $table->string('name_km', 100);
            $table->string('name_en', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();  // Adds both created_at and updated_at
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


            // Add indexes for common queries
            $table->index('name_km');                     // Search by Khmer name
            $table->index('name_en');                     // Search by English name
            $table->index('is_active');                   // Filter active toppings
            $table->index(['created_at', 'is_active']);   // Recent active toppings
            $table->index(['deleted_at', 'is_active']);   // Soft delete filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toppings');
    }
};
