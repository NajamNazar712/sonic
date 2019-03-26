<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingCorporateWeightChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->integer('delivery_type_id');
            $table->float('range_up', 8, 2);
            $table->float('range_down', 8, 2);
            $table->integer('local_or_6hr');
            $table->integer('national_charges_class_0');
            $table->string('national_charges_class_1');
            $table->string('national_charges_class_2');
            $table->string('national_charges_class_3');
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
        Schema::dropIfExists('pending_corporate_weight_charges');
    }
}
