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
        Schema::create('rider_transfer_note_request_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_note_id')->index();
            $table->unsignedBigInteger('shipment_id')->index();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->tinyInteger('notification')->nullable();
            $table->tinyInteger('rider_information')->nullable();
            $table->tinyInteger('open_box')->nullable();
            $table->text('remarks')->nullable();
            $table->tinyInteger('ordering')->default(0);
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
        Schema::dropIfExists('rider_transfer_note_request_shipments');
    }
};
