<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnSheetShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_shipments_received_app', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('shipment_id')->index();
            $table->Integer('return_sheet_id')->index();
            $table->enum('scan_via', [1,2]);
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
        Schema::dropIfExists('return_sheet_shipments');
    }
}
