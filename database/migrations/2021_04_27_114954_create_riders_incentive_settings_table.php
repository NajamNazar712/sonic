<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRidersIncentiveSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders_incentive_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_category_id')->index();
            $table->integer('rider_shipment_payment_type_id')->index();
            $table->integer('rider_shipment_weight_range_id')->index();
            $table->integer('value')->default(0);
            $table->integer('added_by')->index();
            $table->integer('last_updated_by')->index()->nullable();
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
        Schema::dropIfExists('riders_incentive_settings');
    }
}
