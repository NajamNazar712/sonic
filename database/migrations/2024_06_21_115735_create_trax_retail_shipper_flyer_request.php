<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxRetailShipperFlyerRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_retail_shipper_flyer_request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_shipper_id');
            $table->integer('type_size_id');
            $table->integer('trax_centre_id');
            $table->bigInteger('qty');
            $table->double('amount');
            $table->integer('status'); // 0 = pending, 1 = confirmed, 2 = Rejected, 3 = completed
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
        Schema::dropIfExists('trax_retail_shipper_flyer_request');

    }
}
