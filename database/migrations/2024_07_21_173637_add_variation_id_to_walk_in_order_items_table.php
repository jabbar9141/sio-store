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
        Schema::table('walk_in_order_items', function (Blueprint $table) {
            $table->dropColumn(['product_variation_id','total_price'])->nullable();
        });
        Schema::table('walk_in_order_items', function (Blueprint $table) {
            $table->bigInteger('product_variation_id')->nullable();
            $table->float('total_price')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('walk_in_order_items', function (Blueprint $table) {
            $table->dropColumn(['product_variation_id','total_price'])->nullable();
        });
    }
};
