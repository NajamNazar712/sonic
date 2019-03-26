<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('invoice_id');
            $table->integer('shipment_id');
            $table->boolean('type');
            $table->decimal('weight_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('cash_handling_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('insurance_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('return_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('fuel_surcharge', 16, 2)->nullable()->default(NULL);
            $table->decimal('replacement_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('try_and_buy_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('packaging_material_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('adjustment_charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('charges', 16, 2)->nullable()->default(NULL);
            $table->decimal('gst', 16, 2)->nullable()->default(NULL);
            $table->decimal('invoice_amount', 16, 2)->nullable()->default(NULL);
            $table->index('invoice_id');
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
        Schema::dropIfExists('invoice_shipments');
    }
}
