<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoltUndeliveredReasonMapCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolt_undelivered_reason_map_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->integer('delivery_note_id')->index();
            $table->integer('reason_id')->index();
            $table->integer('count');
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
        Schema::dropIfExists('bolt_undelivered_reason_map_counts');
    }
}
