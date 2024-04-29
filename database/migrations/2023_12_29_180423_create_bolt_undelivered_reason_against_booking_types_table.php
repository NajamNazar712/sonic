<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoltUndeliveredReasonAgainstBookingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolt_undelivered_reason_against_booking_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reason_id')->index('idx_reason_id'); // Specify index name
            $table->integer('booking_type_id')->index('idx_booking_type_id'); // Specify index name
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
        Schema::dropIfExists('bolt_undelivered_reason_against_booking_types');
    }
}
