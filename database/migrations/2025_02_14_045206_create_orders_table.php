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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('table_number', 10)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('exchange_rate', 10, 4)->default(0);
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
        Schema::dropIfExists('orders');
    }
};
