<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessProjectionShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_projection_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipment');
            $table->timestamps();
            $table->index(['user_id','shipment','created_at','updated_at'],'index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_projection_shipments');
    }
}
