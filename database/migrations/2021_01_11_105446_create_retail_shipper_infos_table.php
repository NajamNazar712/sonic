<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailShipperInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_shipper_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shipper_phone_no')->index();
            $table->string('shipper_name');
            $table->string('shipper_cnic');
            $table->string('shipper_address');
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
        Schema::dropIfExists('retail_shipper_infos');
    }
}
