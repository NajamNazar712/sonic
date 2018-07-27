<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingPaymentShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_payment_shipments', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('pending_payment_id');
            $table->integer('shipment_id');
            $table->boolean('type');
            $table->bigInteger('amount')->default(0);
            $table->decimal('charges', 16, 2)->default(0);
            $table->decimal('gst', 16, 2)->default(0);
            $table->decimal('payable', 16, 2)->default(0);
            $table->primary(['pending_payment_id', 'shipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_payment_shipments');
    }
}
