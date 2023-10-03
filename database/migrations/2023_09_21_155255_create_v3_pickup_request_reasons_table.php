<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_request_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('type'); // using for differnciate messages of reason like(not pick = 0, pick = 1)
            $table->string('status'); // using for to add filter get select reasons from pick and not pick based reasons
            $table->string('active'); // using for soft delete
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
        Schema::dropIfExists('v3_pickup_request_reasons');
    }
}
