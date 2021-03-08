<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailParcelReceivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_parcel_receivings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_cn');
            $table->integer('category');
            $table->integer('retail_user_id')->index();
            $table->integer('total_cash');
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
        Schema::dropIfExists('retail_parcel_receivings');
    }
}
