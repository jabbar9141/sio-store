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
            $table->foreignId('ships_from')->after('vendor_id')->nullable()->constrained('locations');
            $table->float('width')->after('vendor_id')->default(1);
            $table->float('height')->after('vendor_id')->default(1);
            $table->float('weight')->after('vendor_id')->default(1);
            $table->float('length')->after('vendor_id')->default(1);
        });
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
