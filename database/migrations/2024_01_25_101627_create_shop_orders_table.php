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
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id');
            $table->json('metadata')->nullable();
            $table->enum('status', [
                'Pending',
                'Processing',
                'On Hold',
                'Completed',
                'Shipped',
                'Delivered',
                'Cancelled',
                'Refunded',
                'Failed',
                'Pending Payment',
                'Review Required',
                'On Hold Awaiting Payment',
                'Scheduled for Future Delivery',
                'Processing Return',
                'Returned',
            ])->default('Pending');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses');
            $table->foreignId('user_id')->nullable()->constrained('users');
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
        Schema::dropIfExists('shop_orders');
    }
};
