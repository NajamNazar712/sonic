<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderWiseDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_wise_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_note_id')->index();
            $table->dateTime('delivery_note_created_at')->index();
            $table->integer('rider_id')->index();
            $table->string('trax_id')->index();
            $table->string('rider_name');
            $table->integer('hub_id')->index();
            $table->string('hub_name');
            $table->integer('zone_id')->index();
            $table->string('zone_name');
            $table->integer('count');
            $table->integer('total_shipments');
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
        Schema::dropIfExists('rider_wise_delivery_notes');
    }
}
