<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailPendingPaymentShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_pending_payment_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('retail_pending_payment_id');
            $table->integer('shipment_id');
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
        Schema::dropIfExists('retail_pending_payment_shipments');
    }
}
