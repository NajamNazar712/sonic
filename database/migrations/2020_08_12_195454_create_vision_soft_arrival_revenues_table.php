<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisionSoftArrivalRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vision_soft_arrival_revenues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('origin_city_id');
            $table->integer('service_type_id');
            $table->decimal('weight_charges',8, 2);
            $table->decimal('insurance_charges',8, 2);
            $table->decimal('fuel_surcharge',8, 2);
            $table->decimal('packing_charges',8, 2);
            $table->decimal('packaging_charges',8, 2);
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
        Schema::dropIfExists('vision_soft_arrival_revenues');
    }
}
