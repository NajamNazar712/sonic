<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHblKonnectDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hbl_konnect_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_note_id')->index();
            $table->string('rider_name');
            $table->string('rider_cnic');
            $table->string('rider_phone_no');
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
        Schema::dropIfExists('hbl_konnect_delivery_notes');
    }
}
