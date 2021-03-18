<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->date('attendance_date');
            $table->Time('clock_in')->nullable();
            $table->Time('clock_out')->nullable();
            $table->string('clock_in_latitude');
            $table->string('clock_in_longitude');
            $table->string('clock_out_latitude');
            $table->string('clock_out_longitude');
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
        Schema::dropIfExists('rider_attendances');
    }
}
