<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingInvoiceShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_invoice_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shipment_id');
            $table->boolean('type');
            $table->decimal('charges', 16, 2)->default(0);
            $table->decimal('gst', 16, 2)->default(0);
            $table->decimal('invoice_amount', 16, 2)->default(0);
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_invoice_shipments');
    }
}
