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
        Schema::create('retail_shipment_flyer_numbers', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id')->index();
            $table->integer('retail_shipment_id')->index();
            $table->string('flyer_number');
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
        Schema::dropIfExists('retail_shipment_flyer_numbers');
    }
};
