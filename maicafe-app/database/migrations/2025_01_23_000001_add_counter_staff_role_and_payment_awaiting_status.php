<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'counter' role to users table
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'staff', 'rider', 'kitchen', 'counter') NOT NULL DEFAULT 'customer'");
        
        // Update orders table - modify status enum to include 'awaiting_payment' for pay at counter flow
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('awaiting_payment', 'pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        
        // Add column to track who confirmed the payment at counter
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_confirmed_by')) {
                $table->foreignId('payment_confirmed_by')->nullable()->after('payment_reference')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'payment_confirmed_at')) {
                $table->timestamp('payment_confirmed_at')->nullable()->after('payment_confirmed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any orders with awaiting_payment status to pending
        DB::table('orders')->where('status', 'awaiting_payment')->update(['status' => 'pending']);
        
        // Revert status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        
        // Update any counter users to staff
        DB::table('users')->where('role', 'counter')->update(['role' => 'staff']);
        
        // Revert role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'staff', 'rider', 'kitchen') NOT NULL DEFAULT 'customer'");
        
        // Drop added columns
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_confirmed_by')) {
                $table->dropForeign(['payment_confirmed_by']);
                $table->dropColumn('payment_confirmed_by');
            }
            if (Schema::hasColumn('orders', 'payment_confirmed_at')) {
                $table->dropColumn('payment_confirmed_at');
            }
        });
    }
};
