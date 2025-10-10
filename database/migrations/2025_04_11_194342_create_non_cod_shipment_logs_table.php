<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_cod_shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_number')->nullable();
            $table->string('trax_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_designation')->nullable();
            $table->integer('shipment_otp')->nullable();
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
        Schema::dropIfExists('non_cod_shipment_logs');
    }
};
