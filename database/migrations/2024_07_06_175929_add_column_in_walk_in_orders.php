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
        Schema::table('walk_in_orders', function (Blueprint $table) {
            $table->dropColumn('vat_no');
            $table->dropColumn('address');
            $table->dropColumn('shipping_address');

        });
        Schema::table('walk_in_orders', function (Blueprint $table) {
           
            $table->string('vat_no')->nullable();
            $table->string('address')->nullable();
            $table->string('shipping_address')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('walk_in_orders', function (Blueprint $table) {
            $table->dropColumn('vat_no');
            $table->dropColumn('address');
            $table->dropColumn('shipping_address');

        });
    }
};
