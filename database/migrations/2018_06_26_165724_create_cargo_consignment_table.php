<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoConsignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_consignments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('origin_city_id');
            $table->integer('destination_city_id');
            $table->integer('junction_city_1_id');
            $table->integer('junction_city_2_id');
            $table->integer('hub_id');
            $table->integer('seal_number');
            $table->integer('shipping_mode_id');
            $table->integer('transport_mode_id');
            $table->integer('transport_mode_vendor_id');
            $table->string('builty_number')->nullable()->default(NULL);
            $table->timestamp('expected_arrival_date')->nullable()->default(NULL);
            $table->tinyInteger('shipments');
            $table->tinyInteger('received_shipments')->nullable()->default(NULL);
            $table->decimal('shipments_weight', 16, 2);
            $table->decimal('actual_weight', 16, 2);
            $table->decimal('weight_charges_per_kg', 8, 2)->nullable()->default(NULL);
            $table->decimal('extra_charges', 8, 2)->nullable()->default(NULL);
            $table->decimal('total_weight_charges', 8, 2)->nullable()->default(NULL);
            $table->integer('sender_id');
            $table->integer('receiver_id')->nullable()->default(NULL);
            $table->tinyInteger('type');
            $table->tinyInteger('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cargo_consignments');
    }
}
