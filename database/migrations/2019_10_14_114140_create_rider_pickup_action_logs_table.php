<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderPickupActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_pickup_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('logged_at');
            $table->integer('type_id');
            $table->integer('pickup_note_id');
            $table->integer('pickup_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rider_pickup_action_logs');
    }
}
