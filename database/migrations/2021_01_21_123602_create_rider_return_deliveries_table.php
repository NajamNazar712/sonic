<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderReturnDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_return_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('added_at');
            $table->integer('return_note_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('rider_id')->index();
            $table->decimal('start_location_latitude', 10, 6);
            $table->decimal('start_location_longitude', 10, 6);
            $table->decimal('actual_location_latitude', 10, 6);
            $table->decimal('actual_location_longitude', 10, 6);
            $table->decimal('distance_from_start_to_actual', 8, 2);
            $table->decimal('current_location_latitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('current_location_longitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('distance_from_current_to_actual', 8, 2)->nullable()->default(NULL);
            $table->string('cnic')->nullable()->default(NULL);
            $table->integer('rider_status_id')->index();
            $table->integer('rider_status_reason_id')->nullable()->default(NULL)->index();
            $table->integer('delivered_status')->index();
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
        Schema::dropIfExists('rider_return_deliveries');
    }
}
