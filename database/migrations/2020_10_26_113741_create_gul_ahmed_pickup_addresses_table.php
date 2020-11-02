<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGulAhmedPickupAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gul_ahmed_pickup_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('warehouse_id');
            $table->string('name');
            $table->integer('pickup_address_id')->index();
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
        Schema::dropIfExists('gul_ahmed_pickup_addresses');
    }
}
