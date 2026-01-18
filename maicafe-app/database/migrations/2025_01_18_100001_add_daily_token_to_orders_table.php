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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('daily_token')->nullable()->after('order_number');
            $table->date('token_date')->nullable()->after('daily_token');
            
            // Index for efficient token lookups
            $table->index(['token_date', 'daily_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['token_date', 'daily_token']);
            $table->dropColumn(['daily_token', 'token_date']);
        });
    }
};
