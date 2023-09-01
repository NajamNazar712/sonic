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
            $table->integer('pickup_type')->default(1)->index();
            $table->integer('shipper_id')->index();
            $table->integer('pickup_address_id')->index();
            $table->integer('booked');
            $table->integer('received')->nullable();
            $table->integer('city_id')->index();
            $table->dateTime('pickup_date')->index();
            $table->integer('time_range_id')->index();
            $table->integer('pickup_type_id')->index();
            $table->decimal('estimated_weight')->nullable();
            $table->integer('status_id')->default(1)->index();
            $table->integer('rider_status')->default(1)->index();
            $table->integer('attempts')->default(0)->index();
            $table->integer('current_rider_id')->nullable()->index();
            $table->integer('last_rider_id')->nullable()->index();
            $table->integer('last_updated_by')->nullable()->index();
            $table->tinyInteger('after_cut_off_time')->nullable();
            $table->tinyInteger('vendor')->nullable()->index();
            $table->tinyInteger('try_and_buy')->nullable()->index();
            $table->tinyInteger('reverse_pickup')->nullable()->index();
            $table->tinyInteger('renew')->default(0)->index();
            $table->string('remarks')->nullable();
            $table->tinyInteger('reminder_status')->default(0)->index();
            $table->tinyInteger('generated_type')->default(0)->index();
            $table->integer('generated_by')->nullable()->index();
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
