<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('booking_type_id');
            $table->integer('pickup_address_id');
            $table->boolean('information_display');
            $table->integer('consignee_city_id');
            $table->string('consignee_name');
            $table->string('consignee_address');
            $table->string('consignee_phone_number_1');
            $table->string('consignee_phone_number_2')->nullable()->default(NULL);
            $table->string('consignee_email')->nullable()->default(NULL);
            $table->string('order_id')->nullable()->default(NULL);
            $table->timestamp('pickup_date')->nullable();
            $table->string('special_instructions')->nullable()->default(NULL);
            $table->decimal('estimated_weight', 8, 2);
            $table->integer('shipping_mode_id');
            $table->integer('same_day_timing_id')->nullable()->default(NULL);
            $table->integer('amount');
            $table->integer('payment_mode_id');
            $table->string('tracking_number')->nullable()->default(NULL);
            $table->integer('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
