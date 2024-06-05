<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxLogisticBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_logistic_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cn_number');
            $table->date('booking_date');
            $table->integer('shipper_id')->index();
            $table->integer('shipper_address_id')->index();
            $table->integer('product_id')->index();
            $table->integer('service_id')->index()->nullable();
            $table->integer('origin_id')->index();
            $table->integer('destination_id')->index();
            $table->integer('route_id')->index()->nullable();
            $table->integer('rider_id')->index();
            $table->integer('total_pieces');
            $table->decimal('total_booking_weight')->nullable();
            $table->decimal('total_volumetric_weight')->nullable();
            $table->decimal('total_dense_weight')->nullable();
            $table->decimal('total_actual_weight')->nullable();
            $table->string('consignment_type',1);
            $table->integer('payment_mode_id')->nullable();
            $table->string('handling_inst')->nullable();
            $table->string('ship_ref_no')->nullable();
            $table->string('shipper_reference')->nullable();
            $table->integer('consignee_city_id')->index()->nullable();
            $table->string('consignee_name');
            $table->string('consignee_address')->nullable();
            $table->string('consignee_phone_1')->nullable();
            $table->string('consignee_phone_2')->nullable();
            $table->string('consignee_email')->nullable();
            $table->tinyInteger('user_type')->nullable(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
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
        Schema::dropIfExists('trax_logistic_bookings');
    }
}
