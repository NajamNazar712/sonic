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
        Schema::create('rider_return_transfer_note_request_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_note_id')->index('rrtnr_req_note_idx');
            $table->unsignedBigInteger('shipment_id')->index('rrtnr_shipment_idx');
            $table->unsignedTinyInteger('status')->default(1);
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
        Schema::dropIfExists('rider_return_transfer_note_request_shipments');
    }
};
