<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_ledgers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('shipment_book_date')->nullable();
            $table->string('particulars')->nullable();
            $table->integer('particular_id')->nullable();
            $table->float('debit', 15, 2)->nullable();
            $table->float('credit', 15, 2)->nullable();
            $table->float('balance', 15, 2)->nullable();
            $table->string('ledger_time')->nullable();
            $table->integer('number_of_shipments')->nullable();
            $table->string('tracking_number')->nullable();
            $table->integer('origin')->nullable();
            $table->integer('destination')->nullable();
            $table->float('cod_amount', 15, 2)->nullable();
            $table->integer('type_of_charges')->nullable();
            $table->float('weight_charges', 15, 2)->nullable();
            $table->float('fuel_surcharge', 15, 2)->nullable();
            $table->float('gst', 15, 2)->nullable();
            $table->float('net_payable', 15, 2)->nullable();
            $table->integer('reference_id')->nullable();
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
        Schema::dropIfExists('shipment_ledgers');
    }
}
