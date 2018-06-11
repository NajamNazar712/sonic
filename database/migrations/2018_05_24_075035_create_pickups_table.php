<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shipper_id');
            $table->integer('pickup_address_id');
            $table->tinyInteger('bookings');
            $table->tinyInteger('pending_bookings')->nullable();
            $table->decimal('total_estimated_weight', 16, 2);
            $table->boolean('pickup_type');
            $table->timestamp('pickup_date');
            $table->integer('rider_id')->nullable();
            $table->timestamp('assigned_date')->nullable();
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
        Schema::dropIfExists('pickups');
    }
}
