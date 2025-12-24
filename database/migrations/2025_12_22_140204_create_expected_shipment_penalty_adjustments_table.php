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
        Schema::create('expected_shipment_penalty_adjustments', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id')->index();
            $table->integer('user_id')->index();
            $table->integer('recorded_shipments');
            $table->integer('average_shipments');
            $table->decimal('percentage_applied', 5,2);
            $table->integer('amount');
            $table->integer('adjustment_type_id');
            $table->integer('status');
            $table->integer('status_updated_by');
            $table->dateTime('status_updated_at')->nullable();
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
        Schema::dropIfExists('expected_shipment_penalty_adjustments');
    }
};
