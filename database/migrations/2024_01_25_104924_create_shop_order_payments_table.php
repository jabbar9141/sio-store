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
        Schema::create('shop_order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('ref');
            $table->string('payment_method');
            $table->foreignId('order_id')->constrained('shop_orders');
            $table->integer('amount')->default(0);
            $table->json('metadata')->nullable();
            $table->enum('status', ['Pending', 'Done', 'Cancelled']);
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
        Schema::dropIfExists('shop_order_payments');
    }
};
