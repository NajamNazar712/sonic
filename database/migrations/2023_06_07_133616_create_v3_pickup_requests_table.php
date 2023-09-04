<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_type')->default(1)->index(); //1 - Regular Pickups, 2 - Walkin
            $table->dateTime('pickup_date')->index();
            $table->integer('shipper_id')->index();
            $table->integer('pickup_address_id')->index();
            $table->integer('booked');
            $table->integer('received')->nullable();
            $table->integer('city_id')->index();
            $table->integer('time_range_id')->index();
            $table->integer('pickup_shipment_type_id')->index();
            $table->integer('pieces');
            $table->decimal('weight')->nullable();
            $table->integer('status_id')->default(1)->index();
            $table->integer('attempts')->default(0)->index();
            $table->integer('current_rider_id')->nullable()->index();
            $table->integer('last_rider_id')->nullable()->index();
            $table->integer('last_updated_by')->nullable()->index();
            $table->tinyInteger('after_cut_off_time')->nullable();
            $table->tinyInteger('vendor')->nullable()->index();
            $table->tinyInteger('try_and_buy')->nullable()->index();
            $table->tinyInteger('reverse_pickup')->nullable()->index();
            $table->tinyInteger('renew')->default(0)->index();
            $table->integer('reschedule_request_id')->nullable()->index();
            $table->string('special_request')->nullable();
            $table->string('walkin_name')->nullable();
            $table->string('walkin_address')->nullable();
            $table->string('walkin_contact')->nullable();
            $table->integer('segment_id')->nullable()->index();
            $table->integer('sub_segment_id')->nullable()->index();
            $table->tinyInteger('generated_type')->default(0)->index(); //0 - shipper, 1 Admin
            $table->integer('generated_by')->index();
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v3_pickup_requests');
    }
}
