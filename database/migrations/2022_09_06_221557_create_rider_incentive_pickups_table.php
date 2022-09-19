<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderIncentivePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_incentive_pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->date('date')->index();
            $table->integer('shipments');
            $table->integer('rate');
            $table->integer('incentive');
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
        Schema::dropIfExists('rider_incentive_pickups');
    }
}
