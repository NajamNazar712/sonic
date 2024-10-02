<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMakePaymentTempTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('make_payment_temp_tables', function (Blueprint $table) {
            $table->increments('id'); // Creates an auto-increment unsigned integer for 'id'
            $table->integer('pending_payment_shipment_id')->unsigned(); // Ensures it's unsigned
            $table->unique('pending_payment_shipment_id'); // Makes 'pending_payment_shipment_id' unique
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('make_payment_temp_tables');
    }
}
