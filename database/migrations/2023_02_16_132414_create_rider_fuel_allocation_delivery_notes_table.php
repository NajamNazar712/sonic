<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderFuelAllocationDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_fuel_allocation_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_fuel_allocation_id')->index('rider_fuel_allocation_id_index');
            $table->integer('delivery_note_id')->index('delivery_note_id_index');
            $table->integer('dncc_amount');
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
        Schema::dropIfExists('rider_fuel_allocation_delivery_notes');
    }
}
