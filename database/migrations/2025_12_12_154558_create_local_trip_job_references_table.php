<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_trip_job_references', function (Blueprint $table) {
            $table->id();
            $table->integer('trip_id')->index();
            $table->tinyInteger('job_type')->comment('1-DN,2-RN,3-Pickup Note');
            $table->integer('reference_id')->index();
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
        Schema::dropIfExists('local_fleet_job_references');
    }
};
