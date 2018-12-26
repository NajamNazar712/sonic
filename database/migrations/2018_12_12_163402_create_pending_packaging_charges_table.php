<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingPackagingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_packaging_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
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
        Schema::dropIfExists('pending_packaging_charges');
    }
}
