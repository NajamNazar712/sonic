<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailDonePaymentShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_done_payment_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('retail_done_payment_id');
            $table->integer('shipment_id')->index();
            $table->tinyInteger('type');
            $table->bigInteger('amount')->default(0);
            $table->decimal('payable', 16,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retail_done_payment_shipments');
    }
}
