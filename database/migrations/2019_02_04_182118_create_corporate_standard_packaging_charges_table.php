<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateStandardPackagingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_standard_packaging_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->integer('sm_flyer');
            $table->integer('md_flyer');
            $table->integer('lg_flyer');
            $table->integer('box_flyer');
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
        Schema::dropIfExists('corporate_standard_packaging_charges');
    }
}
