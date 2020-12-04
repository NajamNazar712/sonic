<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentOnHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_on_hold', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->timestamp('delivery_date');
            $table->timestamp('dispatch_date');
            $table->integer('status')->default(1);
            $table->integer('email_status')->default(0);
            $table->integer('added_by');
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
        Schema::dropIfExists('shipment_on_hold');
    }
}
