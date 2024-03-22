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
            $table->integer('shipper_id');
            $table->integer('shipper_address_id');
            $table->integer('product_id');
            $table->integer('service_id')->nullable();
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->integer('route_id')->nullable();
            $table->integer('rider_id');
            $table->integer('total_pieces');
            $table->decimal('booking_weight')->nullable();
            $table->decimal('volumetric_weight')->nullable();
            $table->decimal('dense_weight')->nullable();
            $table->decimal('actual_weight')->nullable();
            $table->string('consignment_type',1);
            $table->integer('payment_mode_id')->nullable();
            $table->string('handling_inst')->nullable();
            $table->string('ship_ref_no')->nullable();
            $table->integer('consignee_city_id')->nullable();
            $table->string('consignee_name');
            $table->string('consignee_address')->nullable();
            $table->string('consignee_phone_1')->nullable();
            $table->string('consignee_phone_2')->nullable();
            $table->string('consignee_email')->nullable();
            $table->tinyInteger('user_type')->nullable(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
