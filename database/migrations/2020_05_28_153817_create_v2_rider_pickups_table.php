<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2RiderPickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_rider_pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('added_at');
            $table->integer('pickup_note_id')->index();
            $table->integer('pickup_request_id')->index();
            $table->integer('pickup_type')->index();
            $table->decimal('start_location_latitude', 10, 6);
            $table->decimal('start_location_longitude', 10, 6);
            $table->decimal('actual_location_latitude', 10, 6);
            $table->decimal('actual_location_longitude', 10, 6);
            $table->decimal('distance_from_start_to_actual', 8, 2);
            $table->decimal('current_location_latitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('current_location_longitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('distance_from_current_to_actual', 8, 2)->nullable()->default(NULL);
            $table->integer('shipments')->nullable();
            $table->integer('pickup_not_pick_reason_id')->nullable()->default(NULL)->index();
            $table->string('picture_path')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v2_rider_pickups');
    }
}
