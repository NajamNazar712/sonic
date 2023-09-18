<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupNoteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_note_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_note_id')->index();
            $table->integer('pickup_request_id')->index();
            $table->tinyInteger('status')->default(0)->index();
            $table->integer('ordering')->index();
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
        Schema::dropIfExists('v3_pickup_note_requests');
    }
}
