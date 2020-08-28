<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisionSoftDellRetRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vision_soft_dell_ret_revenues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('origin_city_id');
            $table->integer('service_type_id');
            $table->decimal('try_and_buy_charges',8, 2);
            $table->decimal('nsa_osa_charges',8, 2);
            $table->decimal('replacement_charges',8, 2);
            $table->decimal('cash_handling_charges',8, 2);
            $table->decimal('gst',8, 2);
            $table->decimal('return_charges',8, 2);
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
        Schema::dropIfExists('vision_soft_dell_ret_revenues');
    }
}
