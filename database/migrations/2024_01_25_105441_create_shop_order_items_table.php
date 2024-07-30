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
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('shop_orders');
            $table->integer('item_id');
            $table->foreign('item_id')->references('product_id')->on('product');
            $table->string('variant')->nullable();
            $table->integer('qty')->default(1);
            $table->enum('status', [
                'Pending',
                'Processing',
                'On Hold',
                'Completed',
                'Cancelled',
                'Partially Shipped',
                'Partially Delivered',
                'Review Required',
                'On Backorder',
                'Ready for Pickup',
            ])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_order_items');
    }
};
