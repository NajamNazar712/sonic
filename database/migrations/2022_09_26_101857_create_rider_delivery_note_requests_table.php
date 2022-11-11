<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderDeliveryNoteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_delivery_note_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id')->index();
            $table->integer('rider_id')->index();
            $table->integer('route_id')->index();
            $table->integer('shipment_count');
            $table->integer('updated_by')->index()->nullable();
            $table->integer('approved_by')->index()->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->integer('total_cod_amount');
            $table->integer('status')->default(0)->index();
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
        Schema::dropIfExists('rider_delivery_note_requests');
    }
}
