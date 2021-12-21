<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDwsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dws_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->string('dws_machine')->nullable();
            $table->string('dws_package_type')->nullable();
            $table->string('dws_is_uploaded')->nullable();
            $table->string('dws_date')->nullable();
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
        Schema::dropIfExists('dws_details');
    }
}
