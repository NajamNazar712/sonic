<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxShipperDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_shipper_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('trax_product_id');
            $table->integer('trax_service_id')->nullable();
            $table->integer('rider_id')->nullable();
            $table->integer('route_id')->nullable();
            $table->integer('piece_setting_id')->nullable();
            $table->integer('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('trax_shipper_details');
    }
}
