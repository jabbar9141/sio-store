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
        Schema::table('product_variations', function (Blueprint $table) {
            $table->string('retail_available')->nullable();
            $table->string('wholesale_available')->nullable();
            $table->string('whole_sale_price')->nullable();
            $table->text('video_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn(['retail_available','video_url']);
            $table->dropColumn('wholesale_available');
            $table->dropColumn('whole_sale_price');
        });
    }
};
