<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipperStoreIdColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_shipping_info_store_addresses', function (Blueprint $table) {
            $table->text('shipper_store_id')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_shipping_info_store_addresses', function (Blueprint $table) {
            $table->integer('shipper_store_id')->default(null)->change();
        });
    }
}
