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
        if (Schema::hasTable('product')) {
            Schema::table('product', function (Blueprint $table) {
                if (!Schema::hasColumn('product', 'total_variation_quantity')) {
                    $table->bigInteger('total_variation_quantity')->nullable();
                }

                if (!Schema::hasColumn('product', 'total_variation_whole_sale_price')) {
                    $table->double('total_variation_whole_sale_price')->nullable();
                }

                if (!Schema::hasColumn('product', 'total_variation_price')) {
                    $table->double('total_variation_price')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            //
        });
    }
};
