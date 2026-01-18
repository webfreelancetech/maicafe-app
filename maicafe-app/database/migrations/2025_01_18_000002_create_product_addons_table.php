<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create addon groups table (e.g., "Milk Options", "Extra Toppings", "Size Upgrades")
        Schema::create('addon_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Milk Options", "Extra Toppings"
            $table->string('description')->nullable();
            $table->enum('selection_type', ['single', 'multiple'])->default('multiple'); // single = radio, multiple = checkbox
            $table->boolean('is_required')->default(false);
            $table->integer('min_selections')->default(0);
            $table->integer('max_selections')->nullable(); // null = unlimited
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create addons table (individual addon items)
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addon_group_id');
            $table->string('name'); // e.g., "Oat Milk", "Extra Shot", "Whipped Cream"
            $table->decimal('price', 10, 2)->default(0); // Additional price
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('addon_group_id')->references('id')->on('addon_groups')->onDelete('cascade');
        });

        // Pivot table to link products with addon groups
        Schema::create('product_addon_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('addon_group_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('addon_group_id')->references('id')->on('addon_groups')->onDelete('cascade');
            
            $table->unique(['product_id', 'addon_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_addon_group');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('addon_groups');
    }
};
