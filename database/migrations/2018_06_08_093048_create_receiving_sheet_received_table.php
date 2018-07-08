<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingSheetReceivedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_sheet_received', function (Blueprint $table) {
            $table->integer('receiving_sheet_id')->nullable()->default(NULL);
            $table->integer('user_id');
            $table->integer('pickup_address_id');
            $table->integer('shipment_id');
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receiving_sheet_received');
    }
}
