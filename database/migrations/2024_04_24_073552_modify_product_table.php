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
        Schema::table('product', function (Blueprint $table) {
            // $table->json('product_colors')->change();
            $table->float('wholesale_price')->after('product_colors')->default(0);
            $table->boolean('retail_available')->after('product_colors')->default(true);
            $table->boolean('wholesale_available')->after('product_colors')->default(true);
            $table->json('available_regions')->after('product_colors')->nullable();
            $table->boolean('returns_allowed')->after('product_colors')->default(true);
            $table->boolean('admin_approved')->after('product_colors')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
