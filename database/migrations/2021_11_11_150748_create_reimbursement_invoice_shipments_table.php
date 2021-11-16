<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementInvoiceShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_invoice_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('type');
            $table->decimal('charges')->nullable();
            $table->decimal('gst')->nullable();
            $table->decimal('invoice_amount')->nullable();
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
        Schema::dropIfExists('reimbursement_invoice_shipments');
    }
}
