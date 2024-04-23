<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxBookingBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_booking_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->index();
            $table->integer('total_bookings');
            $table->integer('complete_bookings')->default(0);
            $table->integer('pending_bookings')->default(0);
            $table->date('batch_date')->index();
            $table->integer('status_id')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('assign_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('trax_booking_batches');
    }
}
