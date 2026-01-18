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
        // Alter the role enum to include 'kitchen'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'staff', 'rider', 'kitchen') NOT NULL DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any kitchen users to staff
        DB::table('users')->where('role', 'kitchen')->update(['role' => 'staff']);
        
        // Then remove the kitchen option
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'staff', 'rider') NOT NULL DEFAULT 'customer'");
    }
};
