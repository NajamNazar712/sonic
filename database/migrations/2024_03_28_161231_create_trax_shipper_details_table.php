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
            $table->integer('user_id')->index();
//            $table->integer('trax_product_id');
            $table->integer('trax_parent_product_id')->nullable()->index();
            $table->integer('trax_service_id')->nullable()->index();
            $table->integer('rider_id')->nullable()->index();
            $table->integer('route_id')->nullable()->index();
            $table->integer('piece_setting_id')->nullable();
            $table->integer('status')->default(1);
            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
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
