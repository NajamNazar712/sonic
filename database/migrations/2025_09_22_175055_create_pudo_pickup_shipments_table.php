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
        Schema::create('pudo_pickup_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('retail_store_id');
            $table->unsignedInteger('retail_address_id');
            $table->unsignedInteger('shipment_id');
            $table->tinyInteger('retail_type')->default(1); //1-trax center 2-franchise
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
        Schema::dropIfExists('pudo_pickup_shipments');
    }
};
