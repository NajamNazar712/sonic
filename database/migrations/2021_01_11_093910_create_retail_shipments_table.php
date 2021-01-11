<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_type_id')->index();
            $table->integer('shipping_mode')->index();
            $table->integer('destination')->index();
            $table->decimal('weight');
            $table->integer('payment_mode_id')->index();
            $table->string('shipper_phone_no')->index();
            $table->string('shipper_name');
            $table->string('shipper_cnic');
            $table->string('shipper_address');
            $table->integer('trax_box_id')->nullable();
            $table->decimal('total_charges');
            $table->decimal('gst_charges');
            $table->decimal('total_amount');
            $table->decimal('weight_charges')->nullable();
            $table->decimal('cash_handling_charges')->nullable();
            $table->decimal('fue_surcharge')->nullable();
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
        Schema::dropIfExists('retail_shipments');
    }
}
