<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailShipperNameVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_shipper_name_verifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number')->index();
            $table->string('shipper_name');
            $table->string('shipper_cnic')->index();
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
        Schema::dropIfExists('retail_shipper_name_verifications');
    }
}
