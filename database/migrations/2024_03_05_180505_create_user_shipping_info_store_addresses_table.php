<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserShippingInfoStoreAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shipping_info_store_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->default(null);
            $table->integer('user_shipping_infos_id')->index()->default(null);
            $table->integer('shipper_store_id')->default(null);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('user_shipping_info_store_addresses');
    }
}
