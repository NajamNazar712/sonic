<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmsUserInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wms_user_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('warehousing');
            $table->boolean('per_product_charges');
            $table->boolean('per_square_foot_charges');
            $table->boolean('packing_charges');
            $table->boolean('labelling_charges');
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
        Schema::dropIfExists('wms_user_informations');
    }
}
