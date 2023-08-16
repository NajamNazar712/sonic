<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderWiseDeliveryNoteShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_wise_delivery_note_shipments', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('rwdn_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipper_status_id')->index();
            $table->dateTime('delivery_note_date')->index();
            $table->dateTime('updated_date')->index();

            $table->integer('before_11_count')->default(0);
            $table->integer('at_11_count')->default(0);
            $table->integer('at_12_count')->default(0);
            $table->integer('at_13_count')->default(0);
            $table->integer('at_14_count')->default(0);
            $table->integer('at_15_count')->default(0);
            $table->integer('at_16_count')->default(0);
            $table->integer('at_17_count')->default(0);
            $table->integer('at_18_count')->default(0);
            $table->integer('at_19_count')->default(0);
            $table->integer('at_20_count')->default(0);
            $table->integer('at_21_count')->default(0);
            $table->integer('at_22_count')->default(0);
            $table->integer('at_23_count')->default(0);
            $table->integer('after_23_count')->default(0);
            $table->integer('total_count')->default(0);

            $table->integer('updated_via')->index()->nullable();

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
        Schema::dropIfExists('rider_wise_delivery_note_shipments');
    }
}
