<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateMultipleIdsIndexShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['payment_mode_id']);
            $table->index('booking_type_id');
            $table->index('shipping_mode_id');
            $table->index('payment_mode_id');
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
}
