<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();

            // Type: 'fixed' or 'percent'
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('value', 10, 2); // Renamed from discount_amount for clarity

            // Limits and Logic
            $table->decimal('min_spend', 10, 2)->nullable(); // Minimum cart total to use
            $table->integer('usage_limit')->nullable();     // Max uses for this coupon (global)
            $table->integer('used_count')->default(0);      // Current total usage
            $table->integer('user_limit')->nullable();      // Max uses per individual user

            // Status and Time
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();     // When the sale begins
            $table->timestamp('expires_at')->nullable();    // Your requested expiration field
            $table->timestamps();
            $table->softDeletes(); // Recommended: don't hard-delete historical data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
