<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVigilanceNoteShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vigilance_note_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vigilance_note_id')->index();
            $table->integer('note_id')->nullable()->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('verification_type')->index();
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
        Schema::dropIfExists('vigilance_note_shipments');
    }
}
