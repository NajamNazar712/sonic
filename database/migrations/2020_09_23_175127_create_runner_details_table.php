<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunnerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runner_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('runner_id');
            $table->string('driver_name');
            $table->string('vehicle_no');
            $table->string('contact_no');
            $table->integer('status')->default(0);
            $table->integer('created_by');
            $table->integer('completed_by')->nullable();
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
        Schema::dropIfExists('runner_details');
    }
}
