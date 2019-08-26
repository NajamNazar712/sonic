<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentInformationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_information_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->string('old_consignee_name');
            $table->string('new_consignee_name');
            $table->string('old_consignee_address');
            $table->string('new_consignee_address');
            $table->string('old_consignee_phone');
            $table->string('new_consignee_phone');
            $table->string('old_special_instruction')->nullable();
            $table->string('new_special_instruction')->nullable();
            $table->integer('updated_by');
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
        Schema::dropIfExists('shipment_information_logs');
    }
}
