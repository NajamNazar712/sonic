<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWholesaleShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dhl_waybill')->index();
            $table->integer('wholesale_user_id')->index();
            $table->integer('origin_city_id')->index();
            $table->integer('destination_city_id')->index();
            $table->string('type');
            $table->decimal('weight', 8, 2)->index();
            $table->integer('pieces')->index();
            $table->bigInteger('courier_charges');
            $table->bigInteger('other_charges');
            $table->bigInteger('bill_amount');
            $table->integer('status_id')->index();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('wholesale_shipments');
    }
}
