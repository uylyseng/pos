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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 3)->unique();  // e.g., USD, KHR
            $table->string('symbol', 10);         // e.g., $, ៛
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


            // Add indexes for common queries
            $table->index('name');                      // Search by name
            $table->index('code');                      // Already indexed by unique constraint
            $table->index('is_active');                 // Filter active currencies
            $table->index('is_default');                // Find default currency
            $table->index(['is_active', 'is_default']); // Active default currency
            $table->index(['deleted_at', 'is_active']); // Soft delete filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
