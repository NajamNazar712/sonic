<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('pickup_request_id');
            $table->integer('rider_id');
            $table->tinyInteger('pickups');
            $table->tinyInteger('bookings');
            $table->decimal('total_estimated_weight', 16, 2);
            $table->boolean('pickup_type');
            $table->integer('assigned_by_user_id');
            $table->integer('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_notes');
    }
}
