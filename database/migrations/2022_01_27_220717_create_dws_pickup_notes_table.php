<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDwsPickupNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dws_pickup_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_note_id')->index();
            $table->integer('rider_id')->index();
            $table->integer('shipments_count');
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
        Schema::dropIfExists('dws_pickup_notes');
    }
}
