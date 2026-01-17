<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'customer'])->default('customer')->after('phone');
            $table->text('address')->nullable()->after('role');
            $table->integer('loyalty_points')->default(0)->after('address');
            $table->string('loyalty_tier')->default('bronze')->after('loyalty_points');
            $table->string('avatar')->nullable()->after('loyalty_tier');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'address', 'loyalty_points', 'loyalty_tier', 'avatar']);
        });
    }
}


